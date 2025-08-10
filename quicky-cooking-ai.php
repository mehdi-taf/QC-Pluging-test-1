<?php
/*
Plugin Name: Quicky Cooking Content AI
Plugin URI: https://quickycooking.com
Description: Générateur de contenu IA ultra-optimisé pour Quicky Cooking - Recettes, Guides, Comparatifs avec SEO automatique
Version: 1.0.0
Author: Quicky Cooking Team
Text Domain: quicky-cooking-ai
*/

if (!defined('ABSPATH')) {
    exit;
}

// CONSTANTES DU PLUGIN
define('QUICKY_AI_VERSION', '1.0.0');
define('QUICKY_AI_PATH', plugin_dir_path(__FILE__));
define('QUICKY_AI_URL', plugin_dir_url(__FILE__));

// INCLURE TOUS LES FICHIERS DU PLUGIN D'ABORD
$plugin_includes = array(
    'includes/class-quicky-ai-connector.php',
    'includes/class-quicky-meta-boxes.php', 
    'includes/class-quicky-templates.php',
    'includes/quicky-ai-settings.php',
    'includes/quicky-ai-list-page.php',
    'includes/quicky-ai-create-page.php'
);

foreach ($plugin_includes as $file) {
    $file_path = QUICKY_AI_PATH . $file;
    if (file_exists($file_path)) {
        require_once $file_path;
    }
}

// CRÉER AUTOMATIQUEMENT TOUTES LES CATÉGORIES ET TAGS
add_action('init', 'create_quicky_categories_and_tags');
function create_quicky_categories_and_tags() {
    
    // CATÉGORIES APPAREILS POPULAIRES
    $popular_appliances = [
        'air-fryer-recipes' => 'Air Fryer Recipes',
        'instant-pot-recipes' => 'Instant Pot Recipes',
        'slow-cooker-recipes' => 'Slow Cooker Recipes',
        'crockpot-recipes' => 'Crockpot Recipes',
        'multicuiseur-recipes' => 'Multicuiseur Recipes',
        'toaster-oven-recipes' => 'Toaster Oven Recipes'
    ];
    
    // CATÉGORIES APPAREILS SPÉCIALISÉS
    $specialized_appliances = [
        'sous-vide-recipes' => 'Sous-vide Recipes',
        'bread-maker-recipes' => 'Bread Maker Recipes',
        'dehydrator-recipes' => 'Dehydrator Recipes',
        'rice-cooker-recipes' => 'Rice Cooker Recipes',
        'stand-mixer-recipes' => 'Stand Mixer Recipes',
        'pressure-cooker-recipes' => 'Pressure Cooker Recipes'
    ];
    
    // CATÉGORIES CONTENU
    $content_categories = [
        'buying-guides' => 'Buying Guides',
        'product-comparisons' => 'Product Comparisons',
        'kitchen-tips' => 'Kitchen Tips & Tricks',
        'appliance-reviews' => 'Appliance Reviews',
        'cooking-guides' => 'Cooking Guides'
    ];
    
    // CRÉER TOUTES LES CATÉGORIES
    $all_categories = array_merge($popular_appliances, $specialized_appliances, $content_categories);
    
    foreach ($all_categories as $slug => $name) {
        $existing_cat = get_category_by_slug($slug);
        if (!$existing_cat) {
            wp_insert_term($name, 'category', array(
                'slug' => $slug,
                'description' => 'Recipes and content for ' . $name
            ));
        }
    }
    
    // TAGS AUTOMATIQUES PAR APPAREIL
    $appliance_tags = [
        // Air Fryer
        'air-fryer', 'air-frying', 'crispy', 'oil-free', 'healthy-cooking',
        // Instant Pot
        'instant-pot', 'pressure-cooking', 'one-pot', 'quick-meals',
        // Slow Cooker
        'slow-cooking', 'crockpot', 'set-forget', 'comfort-food',
        // General
        'kitchen-appliances', 'easy-recipes', 'quick-prep', 'family-friendly',
        'meal-prep', 'weeknight-dinners', 'batch-cooking'
    ];
    
    // TAGS PAR TYPE DE PLAT
    $dish_type_tags = [
        'appetizers', 'main-dishes', 'desserts', 'breakfast', 'lunch', 'dinner',
        'snacks', 'sides', 'soups', 'stews', 'casseroles', 'roasts'
    ];
    
    // TAGS PAR TEMPS DE CUISSON
    $time_tags = [
        'under-30-minutes', 'under-1-hour', 'quick-meals', 'slow-cook',
        '15-minute-meals', '30-minute-meals'
    ];
    
    // TAGS PAR RÉGIME
    $diet_tags = [
        'vegan', 'vegetarian', 'keto', 'low-carb', 'gluten-free', 'dairy-free',
        'paleo', 'whole30', 'mediterranean', 'low-sodium'
    ];
    
    // CRÉER TOUS LES TAGS
    $all_tags = array_merge($appliance_tags, $dish_type_tags, $time_tags, $diet_tags);
    
    foreach ($all_tags as $tag) {
        if (!get_term_by('slug', $tag, 'post_tag')) {
            wp_insert_term(ucwords(str_replace('-', ' ', $tag)), 'post_tag', array('slug' => $tag));
        }
    }
}

// MENU PRINCIPAL DANS L'ADMIN WORDPRESS
add_action('admin_menu', 'quicky_ai_admin_menu');
function quicky_ai_admin_menu() {
    // Menu principal avec icône personnalisée
    add_menu_page(
        'Quicky Cooking AI',
        'Quicky AI',
        'edit_posts',
        'quicky-ai-dashboard',
        'quicky_ai_dashboard_page',
        'dashicons-food',
        6
    );
    
    // Sous-menus
    add_submenu_page(
        'quicky-ai-dashboard',
        'Tableau de bord',
        '📊 Dashboard',
        'edit_posts',
        'quicky-ai-dashboard',
        'quicky_ai_dashboard_page'
    );
    
    add_submenu_page(
        'quicky-ai-dashboard',
        'Générateur IA',
        '🤖 Créer du contenu',
        'edit_posts',
        'quicky-ai-create',
        'quicky_ai_create_page'
    );
    
    add_submenu_page(
        'quicky-ai-dashboard',
        'Mes contenus IA',
        '📋 Mes contenus',
        'edit_posts',
        'quicky-ai-list',
        'quicky_ai_list_page'
    );
    
    add_submenu_page(
        'quicky-ai-dashboard',
        'Paramètres IA',
        '⚙️ Paramètres',
        'manage_options',
        'quicky-ai-settings',
        'quicky_ai_settings_page'
    );
}

// PAGE DASHBOARD ULTRA-OPTIMISÉE
function quicky_ai_dashboard_page() {
    // Statistiques avancées
    $stats = get_quicky_advanced_stats();
    ?>
    <div class="wrap quicky-admin-dashboard">
        <h1>🍳 Quicky Cooking AI - Tableau de bord</h1>
        
        <!-- Stats Cards -->
        <div class="quicky-stats-grid">
            <div class="stat-card recipes">
                <div class="stat-icon">🍳</div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo $stats['recipes']; ?></div>
                    <div class="stat-label">Recettes générées</div>
                </div>
            </div>
            
            <div class="stat-card guides">
                <div class="stat-icon">📖</div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo $stats['guides']; ?></div>
                    <div class="stat-label">Guides d'achat</div>
                </div>
            </div>
            
            <div class="stat-card comparisons">
                <div class="stat-icon">⚖️</div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo $stats['comparisons']; ?></div>
                    <div class="stat-label">Comparatifs</div>
                </div>
            </div>
            
            <div class="stat-card seo-score">
                <div class="stat-icon">📈</div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo $stats['avg_seo_score']; ?>%</div>
                    <div class="stat-label">Score SEO moyen</div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="quicky-quick-actions">
            <div class="postbox">
                <h2 class="hndle">🚀 Actions rapides</h2>
                <div class="inside">
                    <div class="action-buttons">
                        <a href="?page=quicky-ai-create&type=recipe" class="button-primary action-btn recipe-btn">
                            <span class="dashicons dashicons-food"></span>
                            Nouvelle recette
                        </a>
                        <a href="?page=quicky-ai-create&type=guide" class="button-primary action-btn guide-btn">
                            <span class="dashicons dashicons-book"></span>
                            Guide d'achat
                        </a>
                        <a href="?page=quicky-ai-create&type=comparison" class="button-primary action-btn comparison-btn">
                            <span class="dashicons dashicons-chart-line"></span>
                            Comparatif
                        </a>
                        <a href="?page=quicky-ai-settings" class="button-secondary action-btn settings-btn">
                            <span class="dashicons dashicons-admin-settings"></span>
                            Paramètres
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Content -->
        <div class="quicky-recent-content">
            <div class="postbox">
                <h2 class="hndle">📝 Contenu récent</h2>
                <div class="inside">
                    <?php echo get_quicky_recent_content_table(); ?>
                </div>
            </div>
        </div>
        
        <!-- API Status -->
        <div class="quicky-api-status">
            <div class="postbox">
                <h2 class="hndle">🔧 État du système</h2>
                <div class="inside">
                    <?php echo get_quicky_api_status_widget(); ?>
                </div>
            </div>
        </div>
    </div>
    
    <style>
    .quicky-admin-dashboard .quicky-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin: 20px 0 30px 0;
    }
    
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        transition: transform 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .stat-card.recipes { background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%); }
    .stat-card.guides { background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%); }
    .stat-card.comparisons { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333; }
    .stat-card.seo-score { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    
    .stat-icon {
        font-size: 3em;
        opacity: 0.8;
    }
    
    .stat-number {
        font-size: 2.5em;
        font-weight: bold;
        line-height: 1;
    }
    
    .stat-label {
        font-size: 0.9em;
        opacity: 0.9;
        margin-top: 5px;
    }
    
    .action-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }
    
    .action-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 15px 25px;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    </style>
    <?php
}

// FONCTIONS UTILITAIRES POUR LE DASHBOARD
function get_quicky_advanced_stats() {
    $recipes = count(get_posts([
        'meta_key' => '_quicky_content_type',
        'meta_value' => 'recipe',
        'numberposts' => -1
    ]));
    
    $guides = count(get_posts([
        'meta_key' => '_quicky_content_type',
        'meta_value' => 'buying-guide',
        'numberposts' => -1
    ]));
    
    $comparisons = count(get_posts([
        'meta_key' => '_quicky_content_type',
        'meta_value' => 'comparison',
        'numberposts' => -1
    ]));
    
    return [
        'recipes' => $recipes,
        'guides' => $guides,
        'comparisons' => $comparisons,
        'avg_seo_score' => 85 // Calculé dynamiquement plus tard
    ];
}

function get_quicky_recent_content_table() {
    $recent_posts = get_posts([
        'meta_key' => '_quicky_content_type',
        'numberposts' => 5,
        'post_status' => 'any'
    ]);
    
    if (empty($recent_posts)) {
        return '<p>Aucun contenu généré pour le moment. <a href="?page=quicky-ai-create">Créez votre premier contenu !</a></p>';
    }
    
    $html = '<table class="wp-list-table widefat fixed striped">';
    $html .= '<thead><tr>';
    $html .= '<th>Titre</th><th>Type</th><th>Date</th><th>Statut</th><th>Actions</th>';
    $html .= '</tr></thead><tbody>';
    
    foreach ($recent_posts as $post) {
        $content_type = get_post_meta($post->ID, '_quicky_content_type', true);
        $type_labels = [
            'recipe' => '🍳 Recette',
            'buying-guide' => '📖 Guide',
            'comparison' => '⚖️ Comparatif',
            'blog-article' => '📝 Article'
        ];
        
        $html .= '<tr>';
        $html .= '<td><strong>' . esc_html($post->post_title) . '</strong></td>';
        $html .= '<td>' . ($type_labels[$content_type] ?? 'N/A') . '</td>';
        $html .= '<td>' . date('d/m/Y', strtotime($post->post_date)) . '</td>';
        $html .= '<td><span class="status-' . $post->post_status . '">' . ucfirst($post->post_status) . '</span></td>';
        $html .= '<td>';
        $html .= '<a href="' . get_edit_post_link($post->ID) . '" class="button button-small">Modifier</a> ';
        if ($post->post_status === 'publish') {
            $html .= '<a href="' . get_permalink($post->ID) . '" class="button button-small" target="_blank">Voir</a>';
        }
        $html .= '</td>';
        $html .= '</tr>';
    }
    
    $html .= '</tbody></table>';
    return $html;
}

function get_quicky_api_status_widget() {
    $api_key = get_option('quicky_ai_api_key', '');
    $is_connected = !empty($api_key);
    
    $html = '<div class="api-status-widget">';
    
    if ($is_connected) {
        $html .= '<div class="status-indicator connected">';
        $html .= '<span class="status-dot"></span>';
        $html .= '<strong>API Connectée</strong>';
        $html .= '</div>';
        $html .= '<p>✅ Prêt à générer du contenu</p>';
        $html .= '<p><strong>Modèle actuel :</strong> ' . get_option('quicky_ai_model', 'Non défini') . '</p>';
    } else {
        $html .= '<div class="status-indicator disconnected">';
        $html .= '<span class="status-dot"></span>';
        $html .= '<strong>API Non configurée</strong>';
        $html .= '</div>';
        $html .= '<p>⚠️ <a href="?page=quicky-ai-settings">Configurez votre clé API</a> pour commencer</p>';
    }
    
    $html .= '</div>';
    
    $html .= '<style>
    .api-status-widget .status-indicator {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }
    
    .status-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
    }
    
    .status-indicator.connected .status-dot {
        background: #46b450;
        animation: pulse 2s infinite;
    }
    
    .status-indicator.disconnected .status-dot {
        background: #dc3232;
    }
    
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(70, 180, 80, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(70, 180, 80, 0); }
        100% { box-shadow: 0 0 0 0 rgba(70, 180, 80, 0); }
    }
    </style>';
    
    return $html;
}

// HOOKS D'ACTIVATION ET DÉSACTIVATION
register_activation_hook(__FILE__, 'quicky_ai_activate');
function quicky_ai_activate() {
    create_quicky_categories_and_tags();
    flush_rewrite_rules();
    
    // Créer les options par défaut
    add_option('quicky_ai_version', QUICKY_AI_VERSION);
    add_option('quicky_ai_install_date', current_time('mysql'));
}

register_deactivation_hook(__FILE__, 'quicky_ai_deactivate');
function quicky_ai_deactivate() {
    flush_rewrite_rules();
}

// ENQUEUE ADMIN STYLES
add_action('admin_enqueue_scripts', 'quicky_ai_admin_styles');
function quicky_ai_admin_styles($hook) {
    if (strpos($hook, 'quicky-ai') !== false) {
        wp_enqueue_style('quicky-ai-admin', QUICKY_AI_URL . 'assets/css/admin.css', array(), QUICKY_AI_VERSION);
        wp_enqueue_script('quicky-ai-admin', QUICKY_AI_URL . 'assets/js/admin.js', array('jquery'), QUICKY_AI_VERSION, true);
        
        // Localiser les variables JS
        wp_localize_script('quicky-ai-admin', 'QuickyAdmin', array(
            'nonce' => wp_create_nonce('quicky_ai_nonce'),
            'ajax_url' => admin_url('admin-ajax.php')
        ));
    }
}