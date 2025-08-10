<?php
// includes/quicky-ai-settings.php

if (!defined('ABSPATH')) {
    exit;
}

function quicky_ai_settings_page() {
    
    // Traitement du formulaire
    if (isset($_POST['save_settings'])) {
        update_option('quicky_ai_api_key', sanitize_text_field($_POST['quicky_ai_api_key']));
        update_option('quicky_ai_model', sanitize_text_field($_POST['quicky_ai_model']));
        update_option('quicky_ai_writing_tone', sanitize_text_field($_POST['quicky_ai_writing_tone']));
        update_option('quicky_ai_content_length', sanitize_text_field($_POST['quicky_ai_content_length']));
        update_option('quicky_ai_auto_seo', isset($_POST['quicky_ai_auto_seo']));
        update_option('quicky_ai_auto_schema', isset($_POST['quicky_ai_auto_schema']));
        update_option('quicky_ai_auto_tags', isset($_POST['quicky_ai_auto_tags']));
        
        echo '<div class="notice notice-success"><p>✅ Paramètres sauvegardés avec succès !</p></div>';
    }
    
    // Test API
    if (isset($_POST['test_api'])) {
        $test_result = test_quicky_api_connection();
        if ($test_result['success']) {
            echo '<div class="notice notice-success is-dismissible"><p><strong>✅ Connexion API réussie !</strong> ' . $test_result['message'] . '</p></div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p><strong>❌ Erreur API :</strong> ' . $test_result['message'] . '</p></div>';
        }
    }
    
    // Récupérer les valeurs actuelles
    $api_key = get_option('quicky_ai_api_key', '');
    $selected_model = get_option('quicky_ai_model', 'anthropic/claude-3.5-sonnet');
    $auto_seo = get_option('quicky_ai_auto_seo', true);
    $auto_schema = get_option('quicky_ai_auto_schema', true);
    $auto_tags = get_option('quicky_ai_auto_tags', true);
    $content_length = get_option('quicky_ai_content_length', 'medium');
    $writing_tone = get_option('quicky_ai_writing_tone', 'friendly');
    
    ?>
    <div class="wrap quicky-settings-page">
        <h1>⚙️ Paramètres Quicky Cooking AI</h1>
        
        <!-- Navigation par onglets -->
        <nav class="nav-tab-wrapper quicky-nav-tabs">
            <a href="#api-config" class="nav-tab nav-tab-active" data-tab="api-config">🔑 Configuration API</a>
            <a href="#content-settings" class="nav-tab" data-tab="content-settings">📝 Paramètres Contenu</a>
            <a href="#seo-optimization" class="nav-tab" data-tab="seo-optimization">🚀 Optimisation SEO</a>
        </nav>
        
        <form method="post" action="" class="quicky-settings-form">
            
            <!-- ONGLET 1: Configuration API -->
            <div id="api-config" class="tab-content active">
                <div class="settings-grid">
                    
                    <!-- Configuration API -->
                    <div class="settings-card">
                        <div class="card-header">
                            <h2>🔑 Configuration de l'API</h2>
                            <p>Connectez votre clé OpenRouter pour générer du contenu</p>
                        </div>
                        
                        <div class="card-body">
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="quicky_ai_api_key">Clé API OpenRouter</label>
                                    </th>
                                    <td>
                                        <div class="api-key-input-wrapper">
                                            <input type="password" 
                                                   name="quicky_ai_api_key" 
                                                   id="quicky_ai_api_key" 
                                                   value="<?php echo esc_attr($api_key); ?>" 
                                                   class="regular-text api-key-input" 
                                                   placeholder="sk-or-v1-...">
                                            <button type="button" class="button show-hide-api-key" onclick="toggleApiKeyVisibility()">
                                                👁️ Show
                                            </button>
                                        </div>
                                        <p class="description">
                                            <a href="https://openrouter.ai/keys" target="_blank" class="external-link">
                                                🔗 Obtenir une clé API OpenRouter
                                            </a>
                                            | Coût approximatif: $0.002-$0.015 par génération
                                        </p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="quicky_ai_model">Modèle IA</label>
                                    </th>
                                    <td>
                                        <select name="quicky_ai_model" id="quicky_ai_model" class="regular-text">
                                            <optgroup label="🏆 Recommandés">
                                                <option value="anthropic/claude-3.5-sonnet" <?php selected($selected_model, 'anthropic/claude-3.5-sonnet'); ?>>
                                                    Claude 3.5 Sonnet - $0.015/1K tokens (Excellente qualité)
                                                </option>
                                                <option value="anthropic/claude-3.5-haiku" <?php selected($selected_model, 'anthropic/claude-3.5-haiku'); ?>>
                                                    Claude 3.5 Haiku - $0.001/1K tokens (Bon & économique)
                                                </option>
                                            </optgroup>
                                            <optgroup label="💰 Économiques">
                                                <option value="meta-llama/llama-3.1-70b-instruct" <?php selected($selected_model, 'meta-llama/llama-3.1-70b-instruct'); ?>>
                                                    Llama 3.1 70B - $0.0009/1K tokens (Excellent rapport qualité/prix)
                                                </option>
                                                <option value="google/gemma-2-9b-it" <?php selected($selected_model, 'google/gemma-2-9b-it'); ?>>
                                                    Gemma 2 9B - GRATUIT (Google, plus simple)
                                                </option>
                                            </optgroup>
                                            <optgroup label="🔥 Premium">
                                                <option value="openai/gpt-4o" <?php selected($selected_model, 'openai/gpt-4o'); ?>>
                                                    GPT-4o - $0.025/1K tokens (OpenAI)
                                                </option>
                                            </optgroup>
                                        </select>
                                        <p class="description">
                                            💡 <strong>Claude 3.5 Sonnet</strong> recommandé pour la meilleure qualité. 
                                            <strong>Haiku</strong> pour économiser.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Test de connexion -->
                    <div class="settings-card">
                        <div class="card-header">
                            <h2>🧪 Test de connexion</h2>
                        </div>
                        
                        <div class="card-body">
                            <?php if (!empty($api_key)): ?>
                                <div class="api-status connected">
                                    <div class="status-indicator">
                                        <span class="status-dot green pulse"></span>
                                        <strong>API Connectée</strong>
                                    </div>
                                    <p>✅ Prêt à générer du contenu</p>
                                    
                                    <div class="test-api-section">
                                        <button type="submit" name="test_api" class="button-secondary">
                                            🔍 Tester la connexion
                                        </button>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="api-status disconnected">
                                    <div class="status-indicator">
                                        <span class="status-dot red"></span>
                                        <strong>API Non configurée</strong>
                                    </div>
                                    <p>⚠️ Ajoutez votre clé API pour commencer</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                </div>
            </div>
            
            <!-- ONGLET 2: Paramètres Contenu -->
            <div id="content-settings" class="tab-content">
                <div class="settings-grid">
                    
                    <div class="settings-card">
                        <div class="card-header">
                            <h2>📝 Style de Contenu</h2>
                            <p>Personnalisez le style d'écriture de l'IA</p>
                        </div>
                        
                        <div class="card-body">
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="quicky_ai_writing_tone">Ton d'écriture</label>
                                    </th>
                                    <td>
                                        <select name="quicky_ai_writing_tone" id="quicky_ai_writing_tone">
                                            <option value="friendly" <?php selected($writing_tone, 'friendly'); ?>>😊 Amical & Accessible</option>
                                            <option value="professional" <?php selected($writing_tone, 'professional'); ?>>👔 Professionnel</option>
                                            <option value="casual" <?php selected($writing_tone, 'casual'); ?>>😎 Décontracté</option>
                                            <option value="enthusiastic" <?php selected($writing_tone, 'enthusiastic'); ?>>🎉 Enthousiaste</option>
                                            <option value="expert" <?php selected($writing_tone, 'expert'); ?>>🎓 Expert/Technique</option>
                                        </select>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="quicky_ai_content_length">Longueur du contenu</label>
                                    </th>
                                    <td>
                                        <select name="quicky_ai_content_length" id="quicky_ai_content_length">
                                            <option value="short" <?php selected($content_length, 'short'); ?>>📄 Court (800-1200 mots)</option>
                                            <option value="medium" <?php selected($content_length, 'medium'); ?>>📃 Moyen (1200-2000 mots)</option>
                                            <option value="long" <?php selected($content_length, 'long'); ?>>📜 Long (2000+ mots)</option>
                                            <option value="comprehensive" <?php selected($content_length, 'comprehensive'); ?>>📚 Complet (3000+ mots)</option>
                                        </select>
                                        <p class="description">Plus long = plus de détails mais coût API plus élevé</p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                </div>
            </div>
            
            <!-- ONGLET 3: Optimisation SEO -->
            <div id="seo-optimization" class="tab-content">
                <div class="settings-grid">
                    
                    <div class="settings-card">
                        <div class="card-header">
                            <h2>🚀 Optimisation SEO Automatique</h2>
                            <p>Laissez l'IA optimiser automatiquement votre SEO</p>
                        </div>
                        
                        <div class="card-body">
                            <div class="seo-options">
                                
                                <div class="seo-option">
                                    <label class="seo-toggle">
                                        <input type="checkbox" name="quicky_ai_auto_seo" <?php checked($auto_seo); ?>>
                                        <span class="toggle-slider"></span>
                                        <div class="toggle-content">
                                            <strong>🎯 Optimisation SEO automatique</strong>
                                            <p>Génère automatiquement les titres SEO, meta descriptions, et mots-clés optimisés</p>
                                        </div>
                                    </label>
                                </div>
                                
                                <div class="seo-option">
                                    <label class="seo-toggle">
                                        <input type="checkbox" name="quicky_ai_auto_schema" <?php checked($auto_schema); ?>>
                                        <span class="toggle-slider"></span>
                                        <div class="toggle-content">
                                            <strong>🏷️ Schema Markup automatique</strong>
                                            <p>Ajoute automatiquement les données structurées JSON-LD pour les recettes et guides</p>
                                        </div>
                                    </label>
                                </div>
                                
                                <div class="seo-option">
                                    <label class="seo-toggle">
                                        <input type="checkbox" name="quicky_ai_auto_tags" <?php checked($auto_tags); ?>>
                                        <span class="toggle-slider"></span>
                                        <div class="toggle-content">
                                            <strong>🏷️ Tags automatiques</strong>
                                            <p>Génère et assigne automatiquement les tags pertinents selon le contenu</p>
                                        </div>
                                    </label>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
            
            <!-- Bouton de sauvegarde global -->
            <div class="settings-save-section">
                <p class="submit">
                    <input type="submit" name="save_settings" class="button-primary button-large" value="💾 Sauvegarder tous les paramètres">
                </p>
            </div>
            
        </form>
    </div>
    
    <!-- JavaScript pour la gestion des onglets -->
    <script>
    jQuery(document).ready(function($) {
        // Gestion des onglets principaux
        $('.quicky-nav-tabs .nav-tab').on('click', function(e) {
            e.preventDefault();
            var target = $(this).attr('data-tab');
            
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            
            $('.tab-content').removeClass('active');
            $('#' + target).addClass('active');
        });
    });
    
    function toggleApiKeyVisibility() {
        const input = document.getElementById('quicky_ai_api_key');
        const btn = document.querySelector('.show-hide-api-key');
        
        if (input.type === 'password') {
            input.type = 'text';
            btn.textContent = '🙈 Hide';
        } else {
            input.type = 'password';
            btn.textContent = '👁️ Show';
        }
    }
    </script>
    
    <!-- CSS pour les paramètres -->
    <style>
    .quicky-settings-page {
        max-width: 1200px;
    }
    
    .quicky-nav-tabs {
        margin-bottom: 20px;
    }
    
    .tab-content {
        display: none;
    }
    
    .tab-content.active {
        display: block;
    }
    
    .settings-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 30px;
    }
    
    .settings-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
    }
    
    .card-header h2 {
        margin: 0 0 5px 0;
        color: white;
    }
    
    .card-header p {
        margin: 0;
        opacity: 0.9;
    }
    
    .card-body {
        padding: 25px;
    }
    
    .api-key-input-wrapper {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    
    .api-key-input {
        flex: 1;
    }
    
    .status-indicator {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }
    
    .status-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
    }
    
    .status-dot.green {
        background: #46b450;
    }
    
    .status-dot.red {
        background: #dc3232;
    }
    
    .status-dot.pulse {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(70, 180, 80, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(70, 180, 80, 0); }
        100% { box-shadow: 0 0 0 0 rgba(70, 180, 80, 0); }
    }
    
    .seo-option {
        margin-bottom: 20px;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
    }
    
    .seo-toggle {
        display: flex;
        align-items: center;
        gap: 15px;
        cursor: pointer;
    }
    
    .toggle-slider {
        width: 50px;
        height: 24px;
        background: #ccc;
        border-radius: 12px;
        position: relative;
        transition: background 0.3s;
    }
    
    .toggle-slider:before {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        background: white;
        border-radius: 50%;
        top: 2px;
        left: 2px;
        transition: transform 0.3s;
    }
    
    .seo-toggle input:checked + .toggle-slider {
        background: #4CAF50;
    }
    
    .seo-toggle input:checked + .toggle-slider:before {
        transform: translateX(26px);
    }
    
    .seo-toggle input {
        display: none;
    }
    
    .settings-save-section {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        text-align: center;
        margin-top: 30px;
    }
    
    @media (max-width: 768px) {
        .settings-grid {
            grid-template-columns: 1fr;
        }
    }
    </style>
    <?php
}

// FONCTION DE TEST API
function test_quicky_api_connection() {
    $api_key = get_option('quicky_ai_api_key', '');
    if (empty($api_key)) {
        return array('success' => false, 'message' => 'Aucune clé API configurée');
    }
    
    // Test simple avec un petit prompt
    $test_prompt = "Respond with just 'API connection successful' if you receive this message.";
    
    $body = array(
        'model' => get_option('quicky_ai_model', 'anthropic/claude-3.5-haiku'),
        'messages' => array(
            array('role' => 'user', 'content' => $test_prompt)
        ),
        'max_tokens' => 50
    );
    
    $response = wp_remote_post('https://openrouter.ai/api/v1/chat/completions', array(
        'timeout' => 30,
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json',
            'HTTP-Referer' => home_url()
        ),
        'body' => json_encode($body)
    ));
    
    if (is_wp_error($response)) {
        return array('success' => false, 'message' => $response->get_error_message());
    }
    
    $response_code = wp_remote_retrieve_response_code($response);
    if ($response_code === 200) {
        return array('success' => true, 'message' => 'Modèle: ' . get_option('quicky_ai_model'));
    }
    
    return array('success' => false, 'message' => 'Code erreur: ' . $response_code);
}