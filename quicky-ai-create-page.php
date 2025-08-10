<?php
// includes/quicky-ai-create-page.php

if (!defined('ABSPATH')) {
    exit;
}

function quicky_ai_create_page() {
    // Récupération de la clé API
    $api_key = get_option('quicky_ai_api_key', '');
    
    ?>
    <div class="wrap quicky-create-page">
        <h1>🤖 Créer du Contenu avec l'IA</h1>
        
        
        
        <?php if (empty($api_key)): ?>
            <!-- API non configurée -->
            <div class="notice notice-error">
                <p><strong>⚠️ API non configurée</strong></p>
                <p>Vous devez d'abord configurer votre clé API OpenRouter.</p>
                <p><a href="<?php echo admin_url('admin.php?page=quicky-ai-settings'); ?>" class="button-primary">Configurer maintenant</a></p>
            </div>
        <?php else: ?>
            
            <!-- Interface de création -->
            <div class="quicky-creation-interface">
                
                <!-- Sélecteur de type de contenu -->
                <div class="content-type-selector-section">
                    <div class="postbox">
                        <h2 class="hndle">📝 Choisissez le type de contenu</h2>
                        <div class="inside">
                            <div class="content-type-grid">
                                
                                <div class="content-type-card" data-type="recipe">
                                    <div class="card-icon">🍳</div>
                                    <h3>Recette d'Appareil</h3>
                                    <p>Créez des recettes optimisées pour un appareil de cuisine spécifique</p>
                                    <div class="card-features">
                                        <span class="feature">✅ Instructions étape par étape</span>
                                        <span class="feature">✅ Schema markup automatique</span>
                                        <span class="feature">✅ SEO optimisé</span>
                                    </div>
                                </div>
                                
                                <div class="content-type-card" data-type="buying-guide">
                                    <div class="card-icon">📖</div>
                                    <h3>Guide d'Achat</h3>
                                    <p>Guides complets pour aider vos lecteurs à choisir le bon produit</p>
                                    <div class="card-features">
                                        <span class="feature">✅ Comparaisons détaillées</span>
                                        <span class="feature">✅ Recommandations expertes</span>
                                        <span class="feature">✅ Zones d'affiliation intégrées</span>
                                    </div>
                                </div>
                                
                                <div class="content-type-card" data-type="comparison">
                                    <div class="card-icon">⚖️</div>
                                    <h3>Comparatif Produits</h3>
                                    <p>Comparaisons détaillées entre produits similaires</p>
                                    <div class="card-features">
                                        <span class="feature">✅ Analyse objective</span>
                                        <span class="feature">✅ Tableaux de comparaison</span>
                                        <span class="feature">✅ Verdict final</span>
                                    </div>
                                </div>
                                
                                <div class="content-type-card" data-type="blog-article">
                                    <div class="card-icon">📝</div>
                                    <h3>Article de Blog</h3>
                                    <p>Articles informatifs sur la cuisine et les appareils</p>
                                    <div class="card-features">
                                        <span class="feature">✅ Contenu engageant</span>
                                        <span class="feature">✅ Structure SEO</span>
                                        <span class="feature">✅ Conseils pratiques</span>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Zone de formulaire dynamique -->
                <div id="content-form-container" style="display: none;">
                    <div class="postbox">
                        <h2 class="hndle" id="form-title">📝 Paramètres du contenu</h2>
                        <div class="inside">
                            
                            <form id="quicky-generation-form" method="post">
                                <?php wp_nonce_field('quicky_generate_content', 'quicky_nonce'); ?>
                                <input type="hidden" name="generate_quicky_content" value="1">
                                <input type="hidden" name="content_type" id="selected_content_type" value="">
                                
                                <!-- Les champs seront injectés ici par JavaScript -->
                                <div id="content-fields-container"></div>
                                
                                <div class="generation-controls">
                                    <button type="button" id="back-to-selection" class="button button-secondary">
                                        ← Retour à la sélection
                                    </button>
                                    <button type="submit" id="generate-content-final" class="button button-primary button-large">
                                        🚀 Générer le contenu
                                    </button>
                                </div>
                                
                            </form>
                            
                            <!-- Status de génération -->
                            <div id="generation-status" class="generation-status" style="display: none;">
                                <div class="status-message"></div>
                                <div class="progress-bar">
                                    <div class="progress-fill"></div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                
            </div>
            
        <?php endif; ?>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Sélection du type de contenu
        $('.content-type-card').on('click', function() {
            const contentType = $(this).data('type');
            selectContentType(contentType);
        });
        
        // Retour à la sélection
        $('#back-to-selection').on('click', function() {
            $('#content-form-container').hide();
            $('.content-type-selector-section').show();
            $('.content-type-card').removeClass('selected');
        });
        
        // Soumission du formulaire
        $('#quicky-generation-form').on('submit', function(e) {
            e.preventDefault();
            
            // Vérifier que les champs requis sont remplis
            const requiredFields = $(this).find('[required]');
            let hasErrors = false;
            
            requiredFields.each(function() {
                if (!$(this).val().trim()) {
                    $(this).css('border-color', '#dc3545');
                    hasErrors = true;
                } else {
                    $(this).css('border-color', '');
                }
            });
            
            if (hasErrors) {
                alert('Veuillez remplir tous les champs requis');
                return;
            }
            
            // Commencer la génération
            startGeneration();
        });
        
        function selectContentType(type) {
            $('.content-type-card').removeClass('selected');
            $('.content-type-card[data-type="' + type + '"]').addClass('selected');
            
            $('#selected_content_type').val(type);
            loadContentFields(type);
            
            $('.content-type-selector-section').hide();
            $('#content-form-container').show();
        }
        
        function loadContentFields(type) {
            const container = $('#content-fields-container');
            const title = $('#form-title');
            
            const typeNames = {
                'recipe': '🍳 Nouvelle Recette',
                'buying-guide': '📖 Nouveau Guide d\'Achat', 
                'comparison': '⚖️ Nouveau Comparatif',
                'blog-article': '📝 Nouvel Article'
            };
            
            title.text(typeNames[type] || 'Nouveau Contenu');
            
            // Charger les champs appropriés
            container.html(getFieldsForType(type));
        }
        
        function getFieldsForType(type) {
            switch(type) {
                case 'recipe':
                    return getRecipeFields();
                case 'buying-guide':
                    return getBuyingGuideFields();
                case 'comparison':
                    return getComparisonFields();
                case 'blog-article':
                    return getBlogFields();
                default:
                    return '<p>Type de contenu non reconnu</p>';
            }
        }
        
        function getRecipeFields() {
            return `
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="appliance_type">Appareil de cuisine *</label></th>
                        <td>
                            <select name="appliance_type" id="appliance_type" class="regular-text" required>
                                <option value="">Sélectionnez un appareil...</option>
                                <option value="air-fryer">🍟 Air Fryer</option>
                                <option value="instant-pot">⚡ Instant Pot</option>
                                <option value="slow-cooker">🍲 Slow Cooker</option>
                                <option value="crockpot">🍲 Crockpot</option>
                                <option value="toaster-oven">🔥 Toaster Oven</option>
                                <option value="sous-vide">🌡️ Sous-vide</option>
                                <option value="bread-maker">🍞 Machine à Pain</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="recipe_keyword">Mot-clé principal *</label></th>
                        <td>
                            <input type="text" name="recipe_keyword" id="recipe_keyword" 
                                   class="regular-text" placeholder="ex: ailes de poulet" required>
                            <p class="description">Le plat ou ingrédient principal</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="custom_title">Titre personnalisé</label></th>
                        <td>
                            <input type="text" name="custom_title" id="custom_title" 
                                   class="regular-text" placeholder="Laisser vide pour génération auto">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Paramètres</th>
                        <td>
                            <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                                <div>
                                    <label>Temps (min):</label>
                                    <input type="number" name="cooking_time" value="30" min="5" max="480" style="width: 80px;">
                                </div>
                                <div>
                                    <label>Portions:</label>
                                    <input type="number" name="serves" value="4" min="1" max="20" style="width: 80px;">
                                </div>
                                <div>
                                    <label>Difficulté:</label>
                                    <select name="difficulty" style="width: 100px;">
                                        <option value="easy">Facile</option>
                                        <option value="medium">Moyen</option>
                                        <option value="hard">Difficile</option>
                                    </select>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cuisine_type">Type de cuisine</label></th>
                        <td>
                            <select name="cuisine_type" id="cuisine_type" class="regular-text">
                                <option value="">Non spécifié</option>
                                <option value="american">Américaine</option>
                                <option value="italian">Italienne</option>
                                <option value="asian">Asiatique</option>
                                <option value="mexican">Mexicaine</option>
                                <option value="french">Française</option>
                                <option value="mediterranean">Méditerranéenne</option>
                                <option value="indian">Indienne</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Régimes alimentaires</th>
                        <td>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px;">
                                <label><input type="checkbox" name="dietary_tags[]" value="vegan"> Vegan</label>
                                <label><input type="checkbox" name="dietary_tags[]" value="vegetarian"> Végétarien</label>
                                <label><input type="checkbox" name="dietary_tags[]" value="keto"> Keto</label>
                                <label><input type="checkbox" name="dietary_tags[]" value="gluten-free"> Sans gluten</label>
                                <label><input type="checkbox" name="dietary_tags[]" value="dairy-free"> Sans lactose</label>
                                <label><input type="checkbox" name="dietary_tags[]" value="low-carb"> Low-carb</label>
                            </div>
                        </td>
                    </tr>
                </table>
            `;
        }
        
        function getBuyingGuideFields() {
            return `
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="product_category">Catégorie de produit *</label></th>
                        <td>
                            <input type="text" name="product_category" id="product_category" 
                                   class="regular-text" placeholder="ex: Air Fryers" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="budget_range">Gamme de prix</label></th>
                        <td>
                            <select name="budget_range" id="budget_range" class="regular-text">
                                <option value="budget">Budget (< $100)</option>
                                <option value="mid-range">Milieu de gamme ($100-300)</option>
                                <option value="premium">Premium ($300-600)</option>
                                <option value="luxury">Luxe ($600+)</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="target_audience">Audience cible</label></th>
                        <td>
                            <select name="target_audience" id="target_audience" class="regular-text">
                                <option value="beginners">Débutants</option>
                                <option value="experienced">Expérimentés</option>
                                <option value="families">Familles</option>
                                <option value="couples">Couples</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="key_features">Fonctionnalités clés</label></th>
                        <td>
                            <textarea name="key_features" id="key_features" rows="4" class="large-text"
                                      placeholder="ex: Capacité, Puissance, Facilité d'utilisation..."></textarea>
                        </td>
                    </tr>
                </table>
            `;
        }
        
        function getComparisonFields() {
            return `
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="product_1">Produit 1 *</label></th>
                        <td>
                            <input type="text" name="product_1" id="product_1" 
                                   class="regular-text" placeholder="Nom du premier produit" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="product_2">Produit 2 *</label></th>
                        <td>
                            <input type="text" name="product_2" id="product_2" 
                                   class="regular-text" placeholder="Nom du second produit" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="comparison_focus">Focus de comparaison</label></th>
                        <td>
                            <textarea name="comparison_focus" id="comparison_focus" rows="3" class="large-text"
                                      placeholder="Sur quoi se concentrer dans la comparaison..."></textarea>
                        </td>
                    </tr>
                </table>
            `;
        }
        
        function getBlogFields() {
            return `
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="main_topic">Sujet principal *</label></th>
                        <td>
                            <input type="text" name="main_topic" id="main_topic" 
                                   class="regular-text" placeholder="ex: organisation de cuisine" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="article_angle">Angle d'approche</label></th>
                        <td>
                            <select name="article_angle" id="article_angle" class="regular-text">
                                <option value="tips">Conseils pratiques</option>
                                <option value="guide">Guide étape par étape</option>
                                <option value="review">Avis et recommandations</option>
                                <option value="trends">Tendances et nouveautés</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="target_keywords">Mots-clés cibles</label></th>
                        <td>
                            <input type="text" name="target_keywords" id="target_keywords" 
                                   class="regular-text" placeholder="mots-clés séparés par des virgules">
                        </td>
                    </tr>
                </table>
            `;
        }
        
        function startGeneration() {
            const $btn = $('#generate-content-final');
            const $status = $('#generation-status');
            
            $btn.prop('disabled', true).text('🔄 Génération en cours...');
            $status.show().find('.status-message').text('🤖 L\'IA travaille sur votre contenu...');
            
            // Animation de la barre de progression
            let progress = 0;
            const progressInterval = setInterval(() => {
                progress += Math.random() * 10;
                if (progress > 95) {
                    progress = 95;
                    clearInterval(progressInterval);
                }
                $('.progress-fill').css('width', progress + '%');
            }, 500);
            
            // Soumission réelle du formulaire après un délai
            setTimeout(() => {
                $('#quicky-generation-form')[0].submit();
            }, 1000);
        }
    });
    </script>
    
    <style>
    .quicky-create-page {
        max-width: 1200px;
    }
    
    .content-type-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin: 20px 0;
    }
    
    .content-type-card {
        background: white;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 25px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .content-type-card:hover {
        border-color: #667eea;
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
    }
    
    .content-type-card.selected {
        border-color: #667eea;
        background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);
    }
    
    .card-icon {
        font-size: 3em;
        margin-bottom: 15px;
        opacity: 0.8;
    }
    
    .content-type-card h3 {
        margin: 0 0 10px 0;
        color: #2c3e50;
        font-size: 1.3em;
    }
    
    .content-type-card p {
        color: #7f8c8d;
        margin-bottom: 15px;
        line-height: 1.5;
    }
    
    .card-features {
        display: flex;
        flex-direction: column;
        gap: 5px;
        align-items: center;
    }
    
    .feature {
        font-size: 12px;
        color: #27ae60;
        font-weight: 600;
    }
    
    .generation-controls {
        margin-top: 30px;
        display: flex;
        gap: 15px;
        justify-content: space-between;
        align-items: center;
    }
    
    .generation-status {
        margin-top: 30px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid #667eea;
    }
    
    .status-message {
        font-weight: 600;
        margin-bottom: 15px;
        color: #2c3e50;
        text-align: center;
    }
    
    .progress-bar {
        height: 8px;
        background: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
    }
    
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #667eea, #764ba2);
        border-radius: 4px;
        transition: width 0.3s ease;
        width: 0%;
    }
    
    /* Responsive design */
    @media (max-width: 768px) {
        .content-type-grid {
            grid-template-columns: 1fr;
        }
        
        .generation-controls {
            flex-direction: column;
            gap: 10px;
        }
        
        .generation-controls button {
            width: 100%;
        }
    }
    
    /* Style pour les formulaires */
    .form-table th {
        width: 200px;
        vertical-align: top;
        padding-top: 15px;
    }
    
    .form-table td {
        padding: 10px 0;
    }
    
    .form-table input[type="text"],
    .form-table input[type="number"],
    .form-table select,
    .form-table textarea {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 8px 12px;
        font-size: 14px;
    }
    
    .form-table input[type="text"]:focus,
    .form-table input[type="number"]:focus,
    .form-table select:focus,
    .form-table textarea:focus {
        border-color: #667eea;
        outline: none;
        box-shadow: 0 0 0 1px #667eea;
    }
    
    .form-table .description {
        font-style: italic;
        color: #666;
        margin-top: 5px;
    }
    </style>
    <?php
}
?>