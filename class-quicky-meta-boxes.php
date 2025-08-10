<?php
// includes/class-quicky-meta-boxes.php

if (!defined('ABSPATH')) {
    exit;
}

class QuickyMetaBoxes {
    
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_meta_scripts'));
    }
    
    public function enqueue_meta_scripts($hook) {
        if ($hook == 'post.php' || $hook == 'post-new.php') {
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_media();
            wp_enqueue_script('quicky-meta-js', QUICKY_AI_URL . 'assets/js/meta-boxes.js', array('jquery'), QUICKY_AI_VERSION, true);
            wp_enqueue_style('quicky-meta-css', QUICKY_AI_URL . 'assets/css/meta-boxes.css', array(), QUICKY_AI_VERSION);
            
            wp_localize_script('quicky-meta-js', 'QuickyMeta', array(
                'nonce' => wp_create_nonce('quicky_meta_nonce'),
                'ajax_url' => admin_url('admin-ajax.php')
            ));
        }
    }
    
    public function add_meta_boxes() {
        global $post;
        
        if (!$post) return;
        
        $content_type = get_post_meta($post->ID, '_quicky_content_type', true);
        
        // Détecter le type depuis l'URL si nouveau post
        if (empty($content_type) && isset($_GET['content_type'])) {
            $content_type = sanitize_text_field($_GET['content_type']);
        }
        
        // META BOX PRINCIPAL - GÉNÉRATEUR IA
        add_meta_box(
            'quicky_ai_generator',
            '🤖 Générateur Quicky Cooking IA',
            array($this, 'ai_generator_callback'),
            'post',
            'normal',
            'high'
        );
        
        // META BOX SEO BOOST
        add_meta_box(
            'quicky_seo_boost',
            '🚀 SEO Boost Ultra',
            array($this, 'seo_boost_callback'),
            'post',
            'side',
            'high'
        );
        
        // META BOX LIENS D'AFFILIATION
        add_meta_box(
            'quicky_affiliate_manager',
            '💰 Gestionnaire d\'Affiliation',
            array($this, 'affiliate_manager_callback'),
            'post',
            'side',
            'default'
        );
        
        // META BOXES SPÉCIFIQUES SELON LE TYPE
        switch ($content_type) {
            case 'recipe':
                $this->add_recipe_meta_boxes();
                break;
            case 'buying-guide':
                $this->add_buying_guide_meta_boxes();
                break;
            case 'comparison':
                $this->add_comparison_meta_boxes();
                break;
            case 'blog-article':
                $this->add_blog_meta_boxes();
                break;
        }
    }
    
    // ========================================
    // META BOX GÉNÉRATEUR IA PRINCIPAL
    // ========================================
    public function ai_generator_callback($post) {
        wp_nonce_field('quicky_meta_nonce', 'quicky_meta_nonce');
        
        $content_type = get_post_meta($post->ID, '_quicky_content_type', true);
        $api_key = get_option('quicky_ai_api_key', '');
        $is_generated = get_post_meta($post->ID, '_quicky_generated_content', true);
        
        ?>
        <div class="quicky-ai-generator-container">
            <?php if (!empty($is_generated)): ?>
                <!-- Contenu déjà généré -->
                <div class="quicky-generated-notice">
                    <div class="notice notice-success inline">
                        <p><strong>✅ Contenu généré par IA</strong> - Ce post a été créé avec l'intelligence artificielle</p>
                    </div>
                    <div class="regeneration-options">
                        <button type="button" class="button button-secondary" id="quicky-regenerate-content">
                            🔄 Régénérer le contenu
                        </button>
                        <button type="button" class="button button-primary" id="quicky-enhance-content">
                            ✨ Améliorer avec l'IA
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <!-- Interface de génération -->
                <div class="quicky-generation-interface">
                    <div class="generation-header">
                        <h3>🚀 Génération de contenu IA</h3>
                        <p>Créez du contenu optimisé en quelques clics</p>
                    </div>
                    
                    <?php if (empty($api_key)): ?>
                        <div class="api-warning">
                            <div class="notice notice-error inline">
                                <p><strong>⚠️ API non configurée</strong></p>
                                <p><a href="<?php echo admin_url('admin.php?page=quicky-ai-settings'); ?>" class="button button-primary">Configurer maintenant</a></p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="content-type-selector">
                            <label for="quicky-content-type"><strong>Type de contenu :</strong></label>
                            <select id="quicky-content-type" name="quicky_content_type" class="widefat">
                                <option value="" <?php selected($content_type, ''); ?>>Sélectionnez un type...</option>
                                <option value="recipe" <?php selected($content_type, 'recipe'); ?>>🍳 Recette d'appareil</option>
                                <option value="buying-guide" <?php selected($content_type, 'buying-guide'); ?>>📖 Guide d'achat</option>
                                <option value="comparison" <?php selected($content_type, 'comparison'); ?>>⚖️ Comparatif produits</option>
                                <option value="blog-article" <?php selected($content_type, 'blog-article'); ?>>📝 Article de blog</option>
                            </select>
                        </div>
                        
                        <div id="quicky-generation-fields"></div>
                        
                        <div class="generation-controls">
                            <button type="button" id="quicky-generate-content" class="button button-primary button-large" disabled>
                                <span class="dashicons dashicons-superhero"></span>
                                Générer avec l'IA
                            </button>
                        </div>
                        
                        <div id="quicky-generation-status" class="generation-status hidden">
                            <div class="status-message"></div>
                            <div class="progress-bar">
                                <div class="progress-fill"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <input type="hidden" name="quicky_content_type" value="<?php echo esc_attr($content_type); ?>">
        <?php
    }
    
    // ========================================
    // META BOX SEO BOOST
    // ========================================
    public function seo_boost_callback($post) {
        $seo_data = get_post_meta($post->ID, '_quicky_seo_data', true);
        $schema_markup = get_post_meta($post->ID, '_quicky_schema_markup', true);
        $seo_score = get_post_meta($post->ID, '_quicky_seo_score', true) ?: 0;
        
        ?>
        <div class="quicky-seo-boost-container">
            <div class="seo-score-display">
                <div class="score-circle" data-score="<?php echo $seo_score; ?>">
                    <span class="score-number"><?php echo $seo_score; ?></span>
                    <span class="score-label">SEO Score</span>
                </div>
                <div class="score-status">
                    <?php if ($seo_score >= 90): ?>
                        <span class="status excellent">🏆 Excellent</span>
                    <?php elseif ($seo_score >= 75): ?>
                        <span class="status good">✅ Bon</span>
                    <?php elseif ($seo_score >= 50): ?>
                        <span class="status average">⚠️ Moyen</span>
                    <?php else: ?>
                        <span class="status poor">❌ À améliorer</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="seo-checklist">
                <h4>📋 Checklist SEO</h4>
                <ul class="seo-checks">
                    <li class="check-item" data-check="title">
                        <span class="check-status">⏳</span>
                        Titre optimisé (50-60 caractères)
                    </li>
                    <li class="check-item" data-check="meta-desc">
                        <span class="check-status">⏳</span>
                        Meta description (150-160 caractères)
                    </li>
                    <li class="check-item" data-check="headings">
                        <span class="check-status">⏳</span>
                        Structure des titres (H1, H2, H3)
                    </li>
                    <li class="check-item" data-check="keywords">
                        <span class="check-status">⏳</span>
                        Mots-clés intégrés naturellement
                    </li>
                    <li class="check-item" data-check="images">
                        <span class="check-status">⏳</span>
                        Images avec alt text
                    </li>
                    <li class="check-item" data-check="schema">
                        <span class="check-status">⏳</span>
                        Schema markup intégré
                    </li>
                </ul>
            </div>
            
            <div class="seo-tools">
                <button type="button" class="button button-secondary" id="analyze-seo">
                    📊 Analyser le SEO
                </button>
                <button type="button" class="button button-primary" id="optimize-seo">
                    🚀 Optimiser automatiquement
                </button>
            </div>
            
            <div class="schema-preview">
                <h4>🏷️ Schema Markup</h4>
                <div class="schema-status">
                    <?php if ($schema_markup): ?>
                        <span class="status-active">✅ Actif</span>
                        <button type="button" class="button button-small" id="view-schema">Voir</button>
                    <?php else: ?>
                        <span class="status-inactive">❌ Non configuré</span>
                        <button type="button" class="button button-small" id="generate-schema">Générer</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }
    
    // ========================================
    // META BOX GESTIONNAIRE D'AFFILIATION
    // ========================================
    public function affiliate_manager_callback($post) {
        $affiliate_products = get_post_meta($post->ID, '_quicky_affiliate_products', true) ?: array();
        $affiliate_strategy = get_post_meta($post->ID, '_quicky_affiliate_strategy', true) ?: 'subtle';
        
        ?>
        <div class="quicky-affiliate-manager">
            <div class="affiliate-strategy">
                <h4>📈 Stratégie d'affiliation</h4>
                <select name="quicky_affiliate_strategy" class="widefat">
                    <option value="subtle" <?php selected($affiliate_strategy, 'subtle'); ?>>Subtile (recommandé)</option>
                    <option value="moderate" <?php selected($affiliate_strategy, 'moderate'); ?>>Modérée</option>
                    <option value="aggressive" <?php selected($affiliate_strategy, 'aggressive'); ?>>Agressive</option>
                </select>
            </div>
            
            <div class="affiliate-products-manager">
                <h4>🛒 Produits d'affiliation</h4>
                <div id="affiliate-products-container">
                    <?php if (!empty($affiliate_products)): ?>
                        <?php foreach ($affiliate_products as $index => $product): ?>
                            <div class="affiliate-product-item" data-index="<?php echo $index; ?>">
                                <div class="product-header">
                                    <span class="product-number">#<?php echo $index + 1; ?></span>
                                    <button type="button" class="remove-product">×</button>
                                </div>
                                
                                <div class="product-fields">
                                    <input type="text" name="affiliate_products[<?php echo $index; ?>][name]" 
                                           placeholder="Nom du produit" 
                                           value="<?php echo esc_attr($product['name']); ?>" 
                                           class="widefat product-name">
                                    
                                    <input type="url" name="affiliate_products[<?php echo $index; ?>][link]" 
                                           placeholder="Lien d'affiliation" 
                                           value="<?php echo esc_attr($product['link']); ?>" 
                                           class="widefat product-link">
                                    
                                    <input type="text" name="affiliate_products[<?php echo $index; ?>][price]" 
                                           placeholder="Prix (ex: $99)" 
                                           value="<?php echo esc_attr($product['price']); ?>" 
                                           class="product-price">
                                    
                                    <select name="affiliate_products[<?php echo $index; ?>][priority]" class="product-priority">
                                        <option value="primary" <?php selected($product['priority'], 'primary'); ?>>Priorité haute</option>
                                        <option value="secondary" <?php selected($product['priority'], 'secondary'); ?>>Priorité moyenne</option>
                                        <option value="alternative" <?php selected($product['priority'], 'alternative'); ?>>Alternative</option>
                                    </select>
                                    
                                    <textarea name="affiliate_products[<?php echo $index; ?>][description]" 
                                              placeholder="Description courte du produit" 
                                              rows="2" 
                                              class="widefat product-description"><?php echo esc_textarea($product['description']); ?></textarea>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <div class="add-product-section">
                    <button type="button" id="add-affiliate-product" class="button button-secondary">
                        ➕ Ajouter un produit
                    </button>
                </div>
            </div>
            
            <div class="affiliate-placement">
                <h4>📍 Placement des liens</h4>
                <div class="placement-options">
                    <label>
                        <input type="checkbox" name="affiliate_placement[]" value="intro" checked>
                        Dans l'introduction
                    </label>
                    <label>
                        <input type="checkbox" name="affiliate_placement[]" value="middle" checked>
                        Au milieu du contenu
                    </label>
                    <label>
                        <input type="checkbox" name="affiliate_placement[]" value="conclusion" checked>
                        Dans la conclusion
                    </label>
                    <label>
                        <input type="checkbox" name="affiliate_placement[]" value="sidebar">
                        Dans la sidebar
                    </label>
                </div>
            </div>
            
            <div class="affiliate-performance">
                <h4>📊 Performance estimée</h4>
                <div class="performance-metrics">
                    <div class="metric">
                        <span class="metric-label">Potentiel de conversion:</span>
                        <span class="metric-value" id="conversion-potential">Calculer</span>
                    </div>
                    <div class="metric">
                        <span class="metric-label">Revenue estimé/mois:</span>
                        <span class="metric-value" id="estimated-revenue">$0</span>
                    </div>
                </div>
                <button type="button" class="button button-small" id="calculate-performance">
                    📊 Calculer les performances
                </button>
            </div>
        </div>
        <?php
    }
    
    // ========================================
    // META BOXES SPÉCIFIQUES - RECETTES
    // ========================================
    private function add_recipe_meta_boxes() {
        add_meta_box(
            'quicky_recipe_details',
            '🍳 Détails de la Recette',
            array($this, 'recipe_details_callback'),
            'post',
            'normal',
            'default'
        );
        
        add_meta_box(
            'quicky_recipe_nutrition',
            '🥗 Informations Nutritionnelles',
            array($this, 'recipe_nutrition_callback'),
            'post',
            'side',
            'default'
        );
    }
    
    public function recipe_details_callback($post) {
        // Récupération des données
        $appliance_type = get_post_meta($post->ID, '_quicky_appliance_type', true);
        $prep_time = get_post_meta($post->ID, '_quicky_prep_time', true);
        $cook_time = get_post_meta($post->ID, '_quicky_cook_time', true);
        $total_time = get_post_meta($post->ID, '_quicky_total_time', true);
        $servings = get_post_meta($post->ID, '_quicky_servings', true);
        $difficulty = get_post_meta($post->ID, '_quicky_difficulty', true);
        $cuisine_type = get_post_meta($post->ID, '_quicky_cuisine_type', true);
        $dietary_tags = get_post_meta($post->ID, '_quicky_dietary_tags', true);
        
        ?>
        <div class="quicky-recipe-details">
            <div class="recipe-grid">
                <div class="recipe-basics">
                    <h4>📋 Informations de base</h4>
                    
                    <div class="form-row">
                        <label for="appliance_type">Appareil principal :</label>
                        <select id="appliance_type" name="appliance_type" class="widefat">
                            <option value="">Sélectionner un appareil...</option>
                            <optgroup label="Appareils populaires">
                                <option value="air-fryer" <?php selected($appliance_type, 'air-fryer'); ?>>🍟 Air Fryer</option>
                                <option value="instant-pot" <?php selected($appliance_type, 'instant-pot'); ?>>⚡ Instant Pot</option>
                                <option value="slow-cooker" <?php selected($appliance_type, 'slow-cooker'); ?>>🍲 Slow Cooker</option>
                                <option value="crockpot" <?php selected($appliance_type, 'crockpot'); ?>>🍲 Crockpot</option>
                                <option value="toaster-oven" <?php selected($appliance_type, 'toaster-oven'); ?>>🔥 Toaster Oven</option>
                            </optgroup>
                            <optgroup label="Appareils spécialisés">
                                <option value="sous-vide" <?php selected($appliance_type, 'sous-vide'); ?>>🌡️ Sous-vide</option>
                                <option value="bread-maker" <?php selected($appliance_type, 'bread-maker'); ?>>🍞 Machine à Pain</option>
                                <option value="rice-cooker" <?php selected($appliance_type, 'rice-cooker'); ?>>🍚 Rice Cooker</option>
                                <option value="dehydrator" <?php selected($appliance_type, 'dehydrator'); ?>>🥬 Déshydrateur</option>
                                <option value="stand-mixer" <?php selected($appliance_type, 'stand-mixer'); ?>>🥧 Stand Mixer</option>
                            </optgroup>
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <label for="cuisine_type">Type de cuisine :</label>
                        <select id="cuisine_type" name="cuisine_type" class="widefat">
                            <option value="">Non spécifié</option>
                            <option value="american" <?php selected($cuisine_type, 'american'); ?>>Américaine</option>
                            <option value="italian" <?php selected($cuisine_type, 'italian'); ?>>Italienne</option>
                            <option value="asian" <?php selected($cuisine_type, 'asian'); ?>>Asiatique</option>
                            <option value="mexican" <?php selected($cuisine_type, 'mexican'); ?>>Mexicaine</option>
                            <option value="mediterranean" <?php selected($cuisine_type, 'mediterranean'); ?>>Méditerranéenne</option>
                            <option value="indian" <?php selected($cuisine_type, 'indian'); ?>>Indienne</option>
                            <option value="french" <?php selected($cuisine_type, 'french'); ?>>Française</option>
                        </select>
                    </div>
                </div>
                
                <div class="recipe-timing">
                    <h4>⏱️ Temps de préparation</h4>
                    
                    <div class="time-inputs">
                        <div class="time-input">
                            <label for="prep_time">Préparation (min) :</label>
                            <input type="number" id="prep_time" name="prep_time" value="<?php echo esc_attr($prep_time); ?>" min="1" max="300">
                        </div>
                        
                        <div class="time-input">
                            <label for="cook_time">Cuisson (min) :</label>
                            <input type="number" id="cook_time" name="cook_time" value="<?php echo esc_attr($cook_time); ?>" min="1" max="600">
                        </div>
                        
                        <div class="time-input">
                            <label for="total_time">Total (min) :</label>
                            <input type="number" id="total_time" name="total_time" value="<?php echo esc_attr($total_time); ?>" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="recipe-specs">
                    <h4>👥 Spécifications</h4>
                    
                    <div class="form-row">
                        <label for="servings">Nombre de portions :</label>
                        <input type="number" id="servings" name="servings" value="<?php echo esc_attr($servings); ?>" min="1" max="20" class="small-text">
                    </div>
                    
                    <div class="form-row">
                        <label for="difficulty">Niveau de difficulté :</label>
                        <select id="difficulty" name="difficulty">
                            <option value="easy" <?php selected($difficulty, 'easy'); ?>>🟢 Facile</option>
                            <option value="medium" <?php selected($difficulty, 'medium'); ?>>🟡 Moyen</option>
                            <option value="hard" <?php selected($difficulty, 'hard'); ?>>🔴 Difficile</option>
                        </select>
                    </div>
                </div>
                
                <div class="recipe-dietary">
                    <h4>🥗 Régimes alimentaires</h4>
                    <div class="dietary-checkboxes">
                        <?php
                        $all_dietary_tags = explode(',', $dietary_tags);
                        $dietary_options = array(
                            'vegan' => 'Vegan',
                            'vegetarian' => 'Végétarien', 
                            'keto' => 'Keto',
                            'low-carb' => 'Low-carb',
                            'gluten-free' => 'Sans gluten',
                            'dairy-free' => 'Sans lactose',
                            'paleo' => 'Paleo',
                            'whole30' => 'Whole30'
                        );
                        
                        foreach ($dietary_options as $value => $label) {
                            $checked = in_array($value, $all_dietary_tags) ? 'checked' : '';
                            echo '<label><input type="checkbox" name="dietary_tags[]" value="' . $value . '" ' . $checked . '> ' . $label . '</label>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="recipe-auto-tags">
                <h4>🏷️ Tags automatiques</h4>
                <div class="auto-tags-preview" id="auto-tags-preview">
                    <em>Les tags seront générés automatiquement selon vos sélections</em>
                </div>
                <button type="button" class="button button-secondary" id="preview-tags">
                    👀 Prévisualiser les tags
                </button>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            // Calcul automatique du temps total
            $('#prep_time, #cook_time').on('input', function() {
                var prep = parseInt($('#prep_time').val()) || 0;
                var cook = parseInt($('#cook_time').val()) || 0;
                $('#total_time').val(prep + cook);
            });
            
            // Prévisualisation des tags
            $('#preview-tags').on('click', function() {
                var appliance = $('#appliance_type').val();
                var cuisine = $('#cuisine_type').val();
                var dietary = [];
                
                $('input[name="dietary_tags[]"]:checked').each(function() {
                    dietary.push($(this).val());
                });
                
                var tags = [];
                if (appliance) tags.push(appliance.replace('-', ' '));
                if (cuisine) tags.push(cuisine + ' cuisine');
                tags = tags.concat(dietary);
                tags.push('easy recipes', 'quick meals');
                
                $('#auto-tags-preview').html('<strong>Tags générés:</strong> ' + tags.map(tag => '<span class="tag">' + tag + '</span>').join(' '));
            });
        });
        </script>
        <?php
    }
    
    public function recipe_nutrition_callback($post) {
        $nutrition = get_post_meta($post->ID, '_quicky_nutrition', true);
        if ($nutrition) {
            $nutrition = json_decode($nutrition, true);
        } else {
            $nutrition = array();
        }
        
        ?>
        <div class="quicky-nutrition-manager">
            <div class="nutrition-inputs">
                <div class="nutrition-input">
                    <label>Calories :</label>
                    <input type="number" name="nutrition[calories]" value="<?php echo esc_attr($nutrition['calories'] ?? ''); ?>" placeholder="320">
                </div>
                
                <div class="nutrition-input">
                    <label>Protéines :</label>
                    <input type="text" name="nutrition[protein]" value="<?php echo esc_attr($nutrition['protein'] ?? ''); ?>" placeholder="15g">
                </div>
                
                <div class="nutrition-input">
                    <label>Glucides :</label>
                    <input type="text" name="nutrition[carbs]" value="<?php echo esc_attr($nutrition['carbs'] ?? ''); ?>" placeholder="25g">
                </div>
                
                <div class="nutrition-input">
                    <label>Lipides :</label>
                    <input type="text" name="nutrition[fat]" value="<?php echo esc_attr($nutrition['fat'] ?? ''); ?>" placeholder="18g">
                </div>
                
                <div class="nutrition-input">
                    <label>Fibres :</label>
                    <input type="text" name="nutrition[fiber]" value="<?php echo esc_attr($nutrition['fiber'] ?? ''); ?>" placeholder="3g">
                </div>
                
                <div class="nutrition-input">
                    <label>Sodium :</label>
                    <input type="text" name="nutrition[sodium]" value="<?php echo esc_attr($nutrition['sodium'] ?? ''); ?>" placeholder="450mg">
                </div>
            </div>
            
            <div class="nutrition-tools">
                <button type="button" class="button button-secondary" id="calculate-nutrition">
                    🧮 Calculer automatiquement
                </button>
                <p class="description">Calcul basé sur les ingrédients de la recette</p>
            </div>
        </div>
        <?php
    }
    
    // ========================================
    // META BOXES GUIDES D'ACHAT
    // ========================================
    private function add_buying_guide_meta_boxes() {
        add_meta_box(
            'quicky_buying_guide_details',
            '📖 Détails du Guide d\'Achat',
            array($this, 'buying_guide_details_callback'),
            'post',
            'normal',
            'default'
        );
    }
    
    public function buying_guide_details_callback($post) {
        $product_category = get_post_meta($post->ID, '_quicky_product_category', true);
        $budget_range = get_post_meta($post->ID, '_quicky_budget_range', true);
        $target_audience = get_post_meta($post->ID, '_quicky_target_audience', true);
        $key_features = get_post_meta($post->ID, '_quicky_key_features', true);
        
        ?>
        <div class="quicky-guide-details">
            <div class="guide-grid">
                <div class="guide-basics">
                    <h4>📋 Informations du produit</h4>
                    
                    <div class="form-row">
                        <label for="product_category">Catégorie de produit :</label>
                        <input type="text" id="product_category" name="product_category" 
                               value="<?php echo esc_attr($product_category); ?>" 
                               placeholder="ex: Air Fryers, Stand Mixers..." class="widefat">
                    </div>
                    
                    <div class="form-row">
                        <label for="budget_range">Gamme de prix :</label>
                        <select id="budget_range" name="budget_range" class="widefat">
                            <option value="">Sélectionner...</option>
                            <option value="budget" <?php selected($budget_range, 'budget'); ?>>Budget (moins de $100)</option>
                            <option value="mid-range" <?php selected($budget_range, 'mid-range'); ?>>Milieu de gamme ($100-$300)</option>
                            <option value="premium" <?php selected($budget_range, 'premium'); ?>>Premium ($300-$600)</option>
                            <option value="luxury" <?php selected($budget_range, 'luxury'); ?>>Luxe ($600+)</option>
                            <option value="all-ranges" <?php selected($budget_range, 'all-ranges'); ?>>Toutes gammes</option>
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <label for="target_audience">Audience cible :</label>
                        <select id="target_audience" name="target_audience" class="widefat">
                            <option value="">Sélectionner...</option>
                            <option value="beginners" <?php selected($target_audience, 'beginners'); ?>>Débutants en cuisine</option>
                            <option value="experienced" <?php selected($target_audience, 'experienced'); ?>>Cuisiniers expérimentés</option>
                            <option value="families" <?php selected($target_audience, 'families'); ?>>Familles</option>
                            <option value="couples" <?php selected($target_audience, 'couples'); ?>>Couples</option>
                            <option value="single" <?php selected($target_audience, 'single'); ?>>Personnes seules</option>
                            <option value="professional" <?php selected($target_audience, 'professional'); ?>>Usage semi-professionnel</option>
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <label for="key_features">Fonctionnalités clés à couvrir :</label>
                        <textarea id="key_features" name="key_features" rows="4" class="widefat" 
                                  placeholder="ex: Capacité, Puissance, Facilité d'utilisation, Durabilité..."><?php echo esc_textarea($key_features); ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    // ========================================
    // META BOXES MANQUANTES (STUBS)
    // ========================================
    private function add_comparison_meta_boxes() {
        // TODO: Implémenter les meta boxes pour les comparatifs
    }
    
    private function add_blog_meta_boxes() {
        // TODO: Implémenter les meta boxes pour les articles de blog
    }
    
    // ========================================
    // SAUVEGARDE DES META BOXES
    // ========================================
    public function save_meta_boxes($post_id) {
        if (!isset($_POST['quicky_meta_nonce']) || !wp_verify_nonce($_POST['quicky_meta_nonce'], 'quicky_meta_nonce')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Sauvegarder le type de contenu
        if (isset($_POST['quicky_content_type'])) {
            update_post_meta($post_id, '_quicky_content_type', sanitize_text_field($_POST['quicky_content_type']));
        }
        
        // Sauvegarder les données d'affiliation
        if (isset($_POST['quicky_affiliate_strategy'])) {
            update_post_meta($post_id, '_quicky_affiliate_strategy', sanitize_text_field($_POST['quicky_affiliate_strategy']));
        }
        
        if (isset($_POST['affiliate_products'])) {
            $affiliate_products = array();
            foreach ($_POST['affiliate_products'] as $product) {
                $affiliate_products[] = array(
                    'name' => sanitize_text_field($product['name']),
                    'link' => esc_url_raw($product['link']),
                    'price' => sanitize_text_field($product['price']),
                    'priority' => sanitize_text_field($product['priority']),
                    'description' => sanitize_textarea_field($product['description'])
                );
            }
            update_post_meta($post_id, '_quicky_affiliate_products', $affiliate_products);
        }
        
        // Sauvegarder les données de recette
        $recipe_fields = [
            'appliance_type', 'prep_time', 'cook_time', 'total_time', 'servings', 
            'difficulty', 'cuisine_type'
        ];
        
        foreach ($recipe_fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_quicky_' . $field, sanitize_text_field($_POST[$field]));
            }
        }
        
        // Sauvegarder les tags diététiques
        if (isset($_POST['dietary_tags'])) {
            $dietary_tags = implode(',', array_map('sanitize_text_field', $_POST['dietary_tags']));
            update_post_meta($post_id, '_quicky_dietary_tags', $dietary_tags);
        }
        
        // Sauvegarder les données nutritionnelles
        if (isset($_POST['nutrition'])) {
            update_post_meta($post_id, '_quicky_nutrition', json_encode($_POST['nutrition']));
        }
        
        // Sauvegarder les données de guide d'achat
        $guide_fields = [
            'product_category', 'budget_range', 'target_audience', 'key_features'
        ];
        
        foreach ($guide_fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_quicky_' . $field, 
                    $field === 'key_features' ? sanitize_textarea_field($_POST[$field]) : sanitize_text_field($_POST[$field])
                );
            }
        }
    }
}

// Initialisation
new QuickyMetaBoxes();