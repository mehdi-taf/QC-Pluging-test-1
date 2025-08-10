<?php
// includes/class-quicky-templates-enhanced.php

if (!defined('ABSPATH')) {
    exit;
}

class QuickyTemplatesEnhanced {
    
    public function __construct() {
        add_filter('single_template', array($this, 'load_enhanced_template'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_enhanced_assets'));
        add_action('wp_head', array($this, 'add_enhanced_schema_markup'));
        add_filter('body_class', array($this, 'add_enhanced_body_classes'));
        
        // AJAX pour interactions enrichies
        add_action('wp_ajax_track_engagement', array($this, 'track_engagement_ajax'));
        add_action('wp_ajax_nopriv_track_engagement', array($this, 'track_engagement_ajax'));
        add_action('wp_ajax_get_nutrition_details', array($this, 'get_nutrition_details_ajax'));
        add_action('wp_ajax_nopriv_get_nutrition_details', array($this, 'get_nutrition_details_ajax'));
    }
    
    public function enqueue_enhanced_assets() {
        if (is_single() && $this->is_enhanced_quicky_content()) {
            // CSS enrichi avec animations avancées
            wp_enqueue_style(
                'quicky-template-enhanced-css',
                QUICKY_AI_URL . 'assets/css/quicky-template-enhanced.css',
                array(),
                QUICKY_AI_VERSION
            );
            
            // JavaScript pour interactions ultra-avancées
            wp_enqueue_script(
                'quicky-template-enhanced-js',
                QUICKY_AI_URL . 'assets/js/quicky-template-enhanced.js',
                array('jquery'),
                QUICKY_AI_VERSION,
                true
            );
            
            // Librairies externes pour fonctionnalités avancées
            wp_enqueue_script('chart-js', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js', array(), '3.9.1', true);
            wp_enqueue_script('aos-js', 'https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js', array(), '2.3.4', true);
            wp_enqueue_style('aos-css', 'https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css', array(), '2.3.4');
            
            // Variables JS enrichies
            wp_localize_script('quicky-template-enhanced-js', 'QuickyEnhanced', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('quicky_enhanced_nonce'),
                'post_id' => get_the_ID(),
                'user_id' => get_current_user_id(),
                'content_type' => get_post_meta(get_the_ID(), '_quicky_content_type', true),
                'quality_score' => get_post_meta(get_the_ID(), '_quicky_content_quality_score', true),
                'engagement_predictions' => get_post_meta(get_the_ID(), '_quicky_engagement_predictions', true)
            ));
        }
    }
    
    public function load_enhanced_template($template) {
        global $post;
        
        if ($post && $this->is_enhanced_quicky_content($post->ID)) {
            $content_type = get_post_meta($post->ID, '_quicky_content_type', true);
            
            switch ($content_type) {
                case 'recipe':
                    return $this->render_enhanced_recipe_template();
                case 'buying-guide':
                    return $this->render_enhanced_buying_guide_template();
                case 'comparison':
                    return $this->render_enhanced_comparison_template();
                case 'blog-article':
                    return $this->render_enhanced_blog_template();
            }
        }
        
        return $template;
    }
    
    private function is_enhanced_quicky_content($post_id = null) {
        if (!$post_id) {
            global $post;
            $post_id = $post->ID;
        }
        
        $content_type = get_post_meta($post_id, '_quicky_content_type', true);
        $enhanced_version = get_post_meta($post_id, '_quicky_enhanced_version', true);
        
        return !empty($content_type) && $enhanced_version === '2.0';
    }
    
    public function add_enhanced_body_classes($classes) {
        if (is_single() && $this->is_enhanced_quicky_content()) {
            $content_type = get_post_meta(get_the_ID(), '_quicky_content_type', true);
            $classes[] = 'quicky-enhanced-content';
            $classes[] = 'quicky-enhanced-' . $content_type;
            
            // Classes pour le niveau de qualité
            $quality_score = get_post_meta(get_the_ID(), '_quicky_content_quality_score', true);
            if ($quality_score >= 90) {
                $classes[] = 'quicky-premium-quality';
            } elseif ($quality_score >= 75) {
                $classes[] = 'quicky-high-quality';
            }
        }
        return $classes;
    }
    
    public function add_enhanced_schema_markup() {
        if (is_single() && $this->is_enhanced_quicky_content()) {
            $schema_markup = get_post_meta(get_the_ID(), '_quicky_schema_markup_enhanced', true);
            if ($schema_markup) {
                echo '<script type="application/ld+json">' . $schema_markup . '</script>' . "\n";
            }
        }
    }
    
    /**
     * TEMPLATE RECETTE ENRICHI AVEC STORYTELLING
     */
    private function render_enhanced_recipe_template() {
        global $post;
        $recipe_data = $this->get_enhanced_recipe_data($post->ID);
        
        get_header(); ?>
        
        <!-- Progress Bar de lecture -->
        <div class="quicky-reading-progress">
            <div class="progress-bar" id="reading-progress"></div>
        </div>
        
        <div class="quicky-enhanced-recipe-container">
            <!-- Hero Section Ultra-Enrichi -->
            <div class="quicky-recipe-hero-enhanced" data-aos="fade-up">
                <div class="hero-overlay-animated"></div>
                <div class="hero-content-enhanced">
                    
                    <!-- Badges de Qualité -->
                    <div class="recipe-quality-badges" data-aos="fade-up" data-aos-delay="100">
                        <?php if ($recipe_data['quality_score'] >= 90): ?>
                            <span class="quality-badge premium">🏆 Premium Recipe</span>
                        <?php endif; ?>
                        
                        <?php if ($recipe_data['appliance_type']): ?>
                            <span class="appliance-badge-enhanced">
                                <?php echo $this->get_appliance_icon($recipe_data['appliance_type']); ?> 
                                <?php echo ucwords(str_replace('-', ' ', $recipe_data['appliance_type'])); ?>
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($recipe_data['difficulty']): ?>
                            <span class="difficulty-badge-enhanced difficulty-<?php echo $recipe_data['difficulty']; ?>">
                                <?php echo $this->get_difficulty_stars($recipe_data['difficulty']); ?> 
                                <?php echo ucfirst($recipe_data['difficulty']); ?>
                            </span>
                        <?php endif; ?>
                        
                        <span class="ai-generated-badge" title="Enhanced by AI">
                            🤖 AI Enhanced
                        </span>
                    </div>
                    
                    <!-- Titre avec animation de typing -->
                    <h1 class="recipe-title-enhanced" data-aos="fade-up" data-aos-delay="200">
                        <?php echo esc_html($recipe_data['title']); ?>
                    </h1>
                    
                    <!-- Storytelling Intro -->
                    <?php if ($recipe_data['storytelling_intro']): ?>
                        <div class="storytelling-intro-section" data-aos="fade-up" data-aos-delay="400">
                            <div class="emotional-hook">
                                <?php echo esc_html($recipe_data['storytelling_intro']['emotional_hook']); ?>
                            </div>
                            <div class="transformation-promise">
                                <?php echo esc_html($recipe_data['storytelling_intro']['transformation_promise']); ?>
                            </div>
                            <?php if (isset($recipe_data['storytelling_intro']['credibility_statement'])): ?>
                                <div class="credibility-statement">
                                    <?php echo esc_html($recipe_data['storytelling_intro']['credibility_statement']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Meta Row Enrichie -->
                    <div class="recipe-meta-row-enhanced" data-aos="fade-up" data-aos-delay="600">
                        <?php if ($recipe_data['prep_time']): ?>
                            <div class="meta-item-enhanced">
                                <div class="meta-icon-animated">⏱️</div>
                                <div class="meta-content">
                                    <span class="meta-label">Prep Time</span>
                                    <span class="meta-value" data-value="<?php echo $recipe_data['prep_time']; ?>"><?php echo $recipe_data['prep_time']; ?> min</span>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($recipe_data['cook_time']): ?>
                            <div class="meta-item-enhanced">
                                <div class="meta-icon-animated">🔥</div>
                                <div class="meta-content">
                                    <span class="meta-label">Cook Time</span>
                                    <span class="meta-value" data-value="<?php echo $recipe_data['cook_time']; ?>"><?php echo $recipe_data['cook_time']; ?> min</span>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($recipe_data['servings']): ?>
                            <div class="meta-item-enhanced">
                                <div class="meta-icon-animated">👥</div>
                                <div class="meta-content">
                                    <span class="meta-label">Serves</span>
                                    <span class="meta-value" data-value="<?php echo $recipe_data['servings']; ?>"><?php echo $recipe_data['servings']; ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($recipe_data['quality_score']): ?>
                            <div class="meta-item-enhanced">
                                <div class="meta-icon-animated">📊</div>
                                <div class="meta-content">
                                    <span class="meta-label">Quality Score</span>
                                    <span class="meta-value quality-score"><?php echo $recipe_data['quality_score']; ?>%</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Actions Enrichies -->
                    <div class="recipe-actions-enhanced" data-aos="fade-up" data-aos-delay="800">
                        <button class="action-btn-enhanced print-recipe" onclick="printEnhancedRecipe()">
                            <span class="btn-icon">🖨️</span> 
                            <span class="btn-text">Print Recipe</span>
                        </button>
                        <button class="action-btn-enhanced save-recipe" onclick="saveToFavoritesEnhanced()">
                            <span class="btn-icon">❤️</span> 
                            <span class="btn-text">Save</span>
                        </button>
                        <button class="action-btn-enhanced share-recipe" onclick="shareEnhancedRecipe()">
                            <span class="btn-icon">📤</span> 
                            <span class="btn-text">Share</span>
                        </button>
                        <button class="action-btn-enhanced nutrition-calc" onclick="openNutritionCalculator()">
                            <span class="btn-icon">🥗</span> 
                            <span class="btn-text">Nutrition</span>
                        </button>
                    </div>
                    
                </div>
            </div>
            
            <!-- Contenu Principal Enrichi -->
            <div class="quicky-recipe-content-enhanced">
                <div class="content-grid-enhanced">
                    
                    <!-- Colonne Principale -->
                    <main class="recipe-main-enhanced">
                        
                        <!-- Section "Pourquoi ça marche" -->
                        <?php if ($recipe_data['why_this_recipe_works']): ?>
                            <section class="recipe-science-section" data-aos="fade-up">
                                <div class="section-header-enhanced">
                                    <h2 class="section-title-enhanced">🧪 Why This Recipe Works</h2>
                                    <div class="section-subtitle">The science behind perfect results</div>
                                </div>
                                
                                <div class="science-content">
                                    <div class="scientific-explanation">
                                        <h4>🔬 The Science</h4>
                                        <p><?php echo esc_html($recipe_data['why_this_recipe_works']['scientific_explanation']); ?></p>
                                    </div>
                                    
                                    <?php if (isset($recipe_data['why_this_recipe_works']['technique_benefits'])): ?>
                                        <div class="technique-benefits">
                                            <h4>⚡ Technique Benefits</h4>
                                            <p><?php echo esc_html($recipe_data['why_this_recipe_works']['technique_benefits']); ?></p>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($recipe_data['why_this_recipe_works']['flavor_development'])): ?>
                                        <div class="flavor-development">
                                            <h4>👄 Flavor Development</h4>
                                            <p><?php echo esc_html($recipe_data['why_this_recipe_works']['flavor_development']); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </section>
                        <?php endif; ?>
                        
                        <!-- Ingrédients Ultra-Interactifs -->
                        <section class="recipe-ingredients-enhanced" data-aos="fade-up" data-aos-delay="200">
                            <div class="section-header-enhanced">
                                <h2 class="section-title-enhanced">🥕 Ingredients</h2>
                                <div class="serving-adjuster-enhanced">
                                    <span class="serving-label">Servings:</span>
                                    <button class="serving-btn-enhanced minus" onclick="adjustServingsEnhanced(-1)">−</button>
                                    <span class="serving-count-enhanced" id="current-servings-enhanced"><?php echo $recipe_data['servings'] ?: 4; ?></span>
                                    <button class="serving-btn-enhanced plus" onclick="adjustServingsEnhanced(1)">+</button>
                                </div>
                            </div>
                            
                            <div class="ingredients-list-enhanced">
                                <?php 
                                $ingredients = $this->parse_enhanced_ingredients($recipe_data['ingredients_with_science']); 
                                foreach ($ingredients as $index => $ingredient): 
                                ?>
                                    <div class="ingredient-item-enhanced" 
                                         data-aos="slide-right" 
                                         data-aos-delay="<?php echo $index * 100; ?>" 
                                         onclick="toggleIngredientEnhanced(this)"
                                         data-ingredient-index="<?php echo $index; ?>">
                                        
                                        <div class="ingredient-checkbox-enhanced">
                                            <div class="checkbox-inner-enhanced"></div>
                                        </div>
                                        
                                        <div class="ingredient-content">
                                            <span class="ingredient-text-enhanced"><?php echo esc_html($ingredient['ingredient']); ?></span>
                                            
                                            <?php if (isset($ingredient['purpose'])): ?>
                                                <div class="ingredient-purpose">
                                                    <span class="purpose-icon">💡</span>
                                                    <span class="purpose-text"><?php echo esc_html($ingredient['purpose']); ?></span>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if (isset($ingredient['substitution'])): ?>
                                                <div class="ingredient-substitution">
                                                    <span class="substitution-icon">🔄</span>
                                                    <span class="substitution-text">Sub: <?php echo esc_html($ingredient['substitution']); ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="ingredient-actions">
                                            <button class="ingredient-info-btn" onclick="showIngredientInfo(<?php echo $index; ?>)">
                                                <span class="info-icon">ℹ️</span>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Shopping List Generator -->
                            <div class="shopping-list-generator">
                                <button class="shopping-list-btn" onclick="generateShoppingList()">
                                    <span class="btn-icon">🛒</span>
                                    Generate Shopping List
                                </button>
                            </div>
                        </section>
                        
                        <!-- Instructions Masterclass -->
                        <section class="recipe-instructions-enhanced" data-aos="fade-up" data-aos-delay="400">
                            <div class="section-header-enhanced">
                                <h2 class="section-title-enhanced">📝 Step-by-Step Masterclass</h2>
                                <div class="instruction-progress-enhanced">
                                    <span class="progress-text">Step <span id="current-step-enhanced">0</span> of <span id="total-steps-enhanced"><?php echo count($this->parse_enhanced_instructions($recipe_data['step_by_step_masterclass'])); ?></span></span>
                                    <div class="progress-bar-enhanced">
                                        <div class="progress-fill-enhanced" id="instruction-progress-enhanced"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="instructions-list-enhanced">
                                <?php 
                                $instructions = $this->parse_enhanced_instructions($recipe_data['step_by_step_masterclass']);
                                foreach ($instructions as $index => $instruction): 
                                ?>
                                    <div class="instruction-step-enhanced" 
                                         data-aos="fade-left" 
                                         data-aos-delay="<?php echo $index * 150; ?>" 
                                         data-step="<?php echo $index + 1; ?>">
                                        
                                        <div class="step-number-enhanced">
                                            <span class="number"><?php echo $index + 1; ?></span>
                                        </div>
                                        
                                        <div class="step-content-enhanced">
                                            <div class="step-action"><?php echo esc_html($instruction['action']); ?></div>
                                            
                                            <?php if (isset($instruction['why_this_step'])): ?>
                                                <div class="step-why">
                                                    <span class="why-icon">🤔</span>
                                                    <strong>Why:</strong> <?php echo esc_html($instruction['why_this_step']); ?>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if (isset($instruction['sensory_cues'])): ?>
                                                <div class="step-cues">
                                                    <span class="cues-icon">👀</span>
                                                    <strong>Look for:</strong> <?php echo esc_html($instruction['sensory_cues']); ?>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if (isset($instruction['timing_explanation'])): ?>
                                                <div class="step-timing">
                                                    <span class="timing-icon">⏰</span>
                                                    <strong>Timing:</strong> <?php echo esc_html($instruction['timing_explanation']); ?>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if (isset($instruction['pro_tip'])): ?>
                                                <div class="step-pro-tip">
                                                    <span class="tip-icon">💡</span>
                                                    <strong>Pro Tip:</strong> <?php echo esc_html($instruction['pro_tip']); ?>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="step-controls">
                                                <button class="step-complete-btn-enhanced" onclick="markStepCompleteEnhanced(this)">
                                                    <span class="complete-icon">✓</span>
                                                    <span class="complete-text">Done</span>
                                                </button>
                                                
                                                <button class="step-timer-btn" onclick="startStepTimer(<?php echo $index; ?>)">
                                                    <span class="timer-icon">⏲️</span>
                                                    <span class="timer-text">Timer</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </section>
                        
                        <!-- Troubleshooting Ultra-Complet -->
                        <?php if ($recipe_data['troubleshooting_comprehensive']): ?>
                            <section class="troubleshooting-comprehensive-section" data-aos="fade-up" data-aos-delay="600">
                                <div class="section-header-enhanced">
                                    <h2 class="section-title-enhanced">🔧 Complete Troubleshooting Guide</h2>
                                    <div class="section-subtitle">Expert solutions for common issues</div>
                                </div>
                                
                                <div class="troubleshooting-grid">
                                    <?php foreach ($recipe_data['troubleshooting_comprehensive'] as $index => $trouble): ?>
                                        <div class="troubleshooting-item-enhanced" data-aos="zoom-in" data-aos-delay="<?php echo $index * 100; ?>">
                                            <div class="trouble-header">
                                                <h4 class="problem">❌ <?php echo esc_html($trouble['problem']); ?></h4>
                                            </div>
                                            
                                            <div class="trouble-content">
                                                <div class="cause">
                                                    <strong>🔍 Cause:</strong> <?php echo esc_html($trouble['cause']); ?>
                                                </div>
                                                <div class="solution">
                                                    <strong>✅ Solution:</strong> <?php echo esc_html($trouble['solution']); ?>
                                                </div>
                                                <div class="prevention">
                                                    <strong>🛡️ Prevention:</strong> <?php echo esc_html($trouble['prevention']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </section>
                        <?php endif; ?>
                        
                        <!-- Variations Créatives -->
                        <?php if ($recipe_data['variations_and_adaptations']): ?>
                            <section class="variations-section-enhanced" data-aos="fade-up" data-aos-delay="800">
                                <div class="section-header-enhanced">
                                    <h2 class="section-title-enhanced">🎨 Creative Variations</h2>
                                    <div class="section-subtitle">Make it your own</div>
                                </div>
                                
                                <div class="variations-grid">
                                    <?php foreach ($recipe_data['variations_and_adaptations'] as $index => $variation): ?>
                                        <div class="variation-card" data-aos="flip-left" data-aos-delay="<?php echo $index * 150; ?>">
                                            <div class="variation-header">
                                                <h4 class="variation-name"><?php echo esc_html($variation['variation_name']); ?></h4>
                                                <?php if (isset($variation['difficulty_change'])): ?>
                                                    <span class="difficulty-indicator"><?php echo esc_html($variation['difficulty_change']); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="variation-content">
                                                <div class="changes">
                                                    <strong>Changes:</strong> <?php echo esc_html($variation['changes']); ?>
                                                </div>
                                                
                                                <?php if (isset($variation['flavor_profile'])): ?>
                                                    <div class="flavor-profile">
                                                        <strong>Flavor:</strong> <?php echo esc_html($variation['flavor_profile']); ?>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if (isset($variation['dietary_benefit'])): ?>
                                                    <div class="dietary-benefit">
                                                        <strong>Good for:</strong> <?php echo esc_html($variation['dietary_benefit']); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </section>
                        <?php endif; ?>
                        
                        <!-- FAQ Optimisée pour Featured Snippets -->
                        <?php if ($recipe_data['faq_for_featured_snippets']): ?>
                            <section class="faq-featured-section" data-aos="fade-up" data-aos-delay="1000">
                                <div class="section-header-enhanced">
                                    <h2 class="section-title-enhanced">❓ Frequently Asked Questions</h2>
                                    <div class="section-subtitle">Everything you need to know</div>
                                </div>
                                
                                <div class="faq-accordion-enhanced">
                                    <?php foreach ($recipe_data['faq_for_featured_snippets'] as $index => $faq): ?>
                                        <div class="faq-item-enhanced" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                                            <div class="faq-question-enhanced" onclick="toggleFaqEnhanced(this)">
                                                <span class="question-text"><?php echo esc_html($faq['question']); ?></span>
                                                <span class="faq-toggle-enhanced">+</span>
                                            </div>
                                            <div class="faq-answer-enhanced">
                                                <p><?php echo esc_html($faq['answer']); ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </section>
                        <?php endif; ?>
                        
                    </main>
                    
                    <!-- Sidebar Enrichie -->
                    <aside class="recipe-sidebar-enhanced">
                        
                        <!-- Nutrition Calculator Interactive -->
                        <?php if ($recipe_data['nutrition']): ?>
                            <div class="sidebar-card-enhanced nutrition-card-enhanced" data-aos="slide-left">
                                <h3 class="card-title-enhanced">🥗 Nutrition Calculator</h3>
                                <div class="nutrition-calculator">
                                    <div class="nutrition-display" id="nutrition-display">
                                        <?php echo $this->render_nutrition_calculator($recipe_data['nutrition'], $recipe_data['servings']); ?>
                                    </div>
                                    
                                    <div class="nutrition-controls">
                                        <button class="nutrition-details-btn" onclick="showDetailedNutrition()">
                                            📊 Detailed Analysis
                                        </button>
                                        <button class="nutrition-compare-btn" onclick="compareNutrition()">
                                            ⚖️ Compare Foods
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Timer Intelligent -->
                        <div class="sidebar-card-enhanced timer-card" data-aos="slide-left" data-aos-delay="200">
                            <h3 class="card-title-enhanced">⏲️ Smart Timer</h3>
                            <div class="smart-timer-container">
                                <div class="timer-display" id="smart-timer-display">00:00</div>
                                <div class="timer-controls">
                                    <button class="timer-btn prep-timer" onclick="startPrepTimer(<?php echo $recipe_data['prep_time'] ?? 15; ?>)">
                                        Prep (<?php echo $recipe_data['prep_time'] ?? 15; ?>min)
                                    </button>
                                    <button class="timer-btn cook-timer" onclick="startCookTimer(<?php echo $recipe_data['cook_time'] ?? 30; ?>)">
                                        Cook (<?php echo $recipe_data['cook_time'] ?? 30; ?>min)
                                    </button>
                                </div>
                                <div class="timer-alerts">
                                    <label>
                                        <input type="checkbox" id="sound-alerts" checked>
                                        Sound alerts
                                    </label>
                                    <label>
                                        <input type="checkbox" id="visual-alerts" checked>
                                        Visual alerts
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Liens d'Affiliation Intelligents -->
                        <?php echo $this->render_smart_affiliate_products($recipe_data); ?>
                        
                        <!-- Recettes Similaires avec IA -->
                        <div class="sidebar-card-enhanced related-recipes-enhanced" data-aos="slide-left" data-aos-delay="400">
                            <h3 class="card-title-enhanced">🔗 AI Recommendations</h3>
                            <div class="ai-recommendations">
                                <?php echo $this->get_ai_recipe_recommendations($recipe_data['appliance_type'], $recipe_data['keywords']); ?>
                            </div>
                        </div>
                        
                        <!-- Navigation Flottante Intelligente -->
                        <div class="floating-nav-enhanced" data-aos="zoom-in" data-aos-delay="1000">
                            <button class="nav-btn-enhanced" onclick="scrollToSectionEnhanced('ingredients')" data-tooltip="Ingredients">
                                <span>🥕</span>
                            </button>
                            <button class="nav-btn-enhanced" onclick="scrollToSectionEnhanced('instructions')" data-tooltip="Instructions">
                                <span>📝</span>
                            </button>
                            <button class="nav-btn-enhanced" onclick="scrollToSectionEnhanced('troubleshooting')" data-tooltip="Troubleshooting">
                                <span>🔧</span>
                            </button>
                            <button class="nav-btn-enhanced" onclick="scrollToSectionEnhanced('variations')" data-tooltip="Variations">
                                <span>🎨</span>
                            </button>
                            <button class="nav-btn-enhanced" onclick="scrollToTopEnhanced()" data-tooltip="Back to Top">
                                <span>⬆️</span>
                            </button>
                        </div>
                        
                    </aside>
                </div>
            </div>
        </div>
        
        <!-- Modals pour fonctionnalités avancées -->
        <?php echo $this->render_enhanced_modals(); ?>
        
        <?php 
        get_footer();
        exit;
    }
    
    /**
     * FONCTIONS UTILITAIRES POUR TEMPLATE ENRICHI
     */
    
    private function get_enhanced_recipe_data($post_id) {
        $enhanced_content = json_decode(get_post_meta($post_id, '_quicky_generated_content_enhanced', true), true);
        
        if (!$enhanced_content) {
            // Fallback vers contenu standard
            $enhanced_content = json_decode(get_post_meta($post_id, '_quicky_generated_content', true), true);
        }
        
        return array(
            'title' => get_the_title($post_id),
            'description' => get_the_excerpt($post_id),
            'appliance_type' => get_post_meta($post_id, '_quicky_appliance_type', true),
            'prep_time' => get_post_meta($post_id, '_quicky_prep_time', true),
            'cook_time' => get_post_meta($post_id, '_quicky_cook_time', true),
            'total_time' => get_post_meta($post_id, '_quicky_total_time', true),
            'servings' => get_post_meta($post_id, '_quicky_servings', true),
            'difficulty' => get_post_meta($post_id, '_quicky_difficulty', true),
            'cuisine_type' => get_post_meta($post_id, '_quicky_cuisine_type', true),
            'nutrition' => json_decode(get_post_meta($post_id, '_quicky_nutrition', true), true),
            'quality_score' => get_post_meta($post_id, '_quicky_content_quality_score', true),
            'keywords' => get_the_tags($post_id),
            
            // Données enrichies
            'storytelling_intro' => $enhanced_content['storytelling_intro'] ?? null,
            'why_this_recipe_works' => $enhanced_content['why_this_recipe_works'] ?? null,
            'ingredients_with_science' => $enhanced_content['main_content']['ingredients_with_science'] ?? [],
            'step_by_step_masterclass' => $enhanced_content['main_content']['step_by_step_masterclass'] ?? [],
            'troubleshooting_comprehensive' => $enhanced_content['main_content']['troubleshooting_comprehensive'] ?? [],
            'variations_and_adaptations' => $enhanced_content['main_content']['variations_and_adaptations'] ?? [],
            'faq_for_featured_snippets' => $enhanced_content['seo_optimized_sections']['faq_for_featured_snippets'] ?? []
        );
    }
    
    private function parse_enhanced_ingredients($ingredients_data) {
        if (empty($ingredients_data)) return [];
        
        return $ingredients_data;
    }
    
    private function parse_enhanced_instructions($instructions_data) {
        if (empty($instructions_data)) return [];
        
        return $instructions_data;
    }
    
    private function render_nutrition_calculator($nutrition, $servings) {
        if (!$nutrition) return '<p>Nutrition information not available</p>';
        
        $html = '<div class="nutrition-grid-enhanced">';
        
        $nutrition_items = [
            'calories' => ['Calories', '🔥', ''],
            'protein' => ['Protein', '💪', 'g'],
            'carbs' => ['Carbs', '🌾', 'g'],
            'fat' => ['Fat', '🥑', 'g'],
            'fiber' => ['Fiber', '🌿', 'g']
        ];
        
        foreach ($nutrition_items as $key => $info) {
            if (isset($nutrition[$key])) {
                $value = $nutrition[$key];
                $html .= '<div class="nutrition-item-enhanced" data-nutrition="' . $key . '">';
                $html .= '<span class="nutrition-icon">' . $info[1] . '</span>';
                $html .= '<span class="nutrition-label">' . $info[0] . '</span>';
                $html .= '<span class="nutrition-value" data-base-value="' . intval($value) . '">' . $value . $info[2] . '</span>';
                $html .= '</div>';
            }
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    private function render_smart_affiliate_products($recipe_data) {
        $affiliate_products = get_post_meta(get_the_ID(), '_quicky_affiliate_products', true);
        
        if (!$affiliate_products) {
            return '<div class="sidebar-card-enhanced affiliate-placeholder-enhanced" data-aos="slide-left" data-aos-delay="300">
                        <h3 class="card-title-enhanced">💰 Recommended Products</h3>
                        <div class="affiliate-empty-enhanced">
                            <p>🔗 Smart product recommendations will appear here!</p>
                            <small>Based on your recipe and cooking preferences.</small>
                        </div>
                    </div>';
        }
        
        $html = '<div class="sidebar-card-enhanced affiliate-products-enhanced" data-aos="slide-left" data-aos-delay="300">';
        $html .= '<h3 class="card-title-enhanced">🛒 Smart Recommendations</h3>';
        
        foreach ($affiliate_products as $index => $product) {
            $html .= '<div class="affiliate-product-enhanced" data-product-index="' . $index . '">';
            $html .= '<div class="product-header-enhanced">';
            $html .= '<h4 class="product-name-enhanced">' . esc_html($product['name']) . '</h4>';
            if ($product['price']) {
                $html .= '<div class="product-price-enhanced">' . esc_html($product['price']) . '</div>';
            }
            $html .= '</div>';
            
            if ($product['description']) {
                $html .= '<p class="product-desc-enhanced">' . esc_html($product['description']) . '</p>';
            }
            
            if ($product['link']) {
                $html .= '<div class="product-actions-enhanced">';
                $html .= '<a href="' . esc_url($product['link']) . '" class="affiliate-btn-enhanced" target="_blank" rel="nofollow" onclick="trackAffiliateClick(\'' . esc_js($product['name']) . '\')">';
                $html .= '<span class="btn-icon">🛒</span> Check Price';
                $html .= '</a>';
                $html .= '<button class="compare-btn-enhanced" onclick="addToComparison(\'' . esc_js($product['name']) . '\')">';
                $html .= '<span class="btn-icon">⚖️</span> Compare';
                $html .= '</button>';
                $html .= '</div>';
            }
            $html .= '</div>';
        }
        
        $html .= '</div>';
        return $html;
    }
    
    private function get_ai_recipe_recommendations($appliance_type, $keywords) {
        // Récupérer des recettes similaires basées sur l'IA
        $related_posts = get_posts(array(
            'numberposts' => 4,
            'post_type' => 'post',
            'meta_query' => array(
                array(
                    'key' => '_quicky_appliance_type',
                    'value' => $appliance_type,
                    'compare' => '='
                )
            ),
            'exclude' => array(get_the_ID())
        ));
        
        if (empty($related_posts)) {
            return '<p class="no-recommendations">More AI recommendations coming soon!</p>';
        }
        
        $html = '<div class="ai-recommendations-list">';
        foreach ($related_posts as $related_post) {
            $quality_score = get_post_meta($related_post->ID, '_quicky_content_quality_score', true);
            
            $html .= '<div class="ai-recommendation-item">';
            $html .= '<a href="' . get_permalink($related_post->ID) . '" class="recommendation-link">';
            
            if (has_post_thumbnail($related_post->ID)) {
                $html .= '<div class="recommendation-image">';
                $html .= get_the_post_thumbnail($related_post->ID, 'thumbnail');
                $html .= '</div>';
            }
            
            $html .= '<div class="recommendation-content">';
            $html .= '<div class="recommendation-title">' . esc_html($related_post->post_title) . '</div>';
            
            $prep_time = get_post_meta($related_post->ID, '_quicky_prep_time', true);
            if ($prep_time) {
                $html .= '<div class="recommendation-meta">⏱️ ' . $prep_time . ' min</div>';
            }
            
            if ($quality_score) {
                $html .= '<div class="recommendation-quality">📊 ' . $quality_score . '% quality</div>';
            }
            
            $html .= '</div>';
            $html .= '</a>';
            $html .= '</div>';
        }
        $html .= '</div>';
        
        return $html;
    }
    
    private function render_enhanced_modals() {
        return '
        <!-- Modal Nutrition Détaillée -->
        <div id="nutrition-modal" class="quicky-modal-enhanced" style="display: none;">
            <div class="modal-content-enhanced">
                <div class="modal-header-enhanced">
                    <h3>📊 Detailed Nutrition Analysis</h3>
                    <button class="modal-close-enhanced" onclick="closeModal(\'nutrition-modal\')">&times;</button>
                </div>
                <div class="modal-body-enhanced" id="detailed-nutrition-content">
                    <!-- Contenu généré dynamiquement -->
                </div>
            </div>
        </div>
        
        <!-- Modal Shopping List -->
        <div id="shopping-modal" class="quicky-modal-enhanced" style="display: none;">
            <div class="modal-content-enhanced">
                <div class="modal-header-enhanced">
                    <h3>🛒 Smart Shopping List</h3>
                    <button class="modal-close-enhanced" onclick="closeModal(\'shopping-modal\')">&times;</button>
                </div>
                <div class="modal-body-enhanced" id="shopping-list-content">
                    <!-- Contenu généré dynamiquement -->
                </div>
            </div>
        </div>
        
        <!-- Modal Ingredient Info -->
        <div id="ingredient-modal" class="quicky-modal-enhanced" style="display: none;">
            <div class="modal-content-enhanced">
                <div class="modal-header-enhanced">
                    <h3>🥕 Ingredient Information</h3>
                    <button class="modal-close-enhanced" onclick="closeModal(\'ingredient-modal\')">&times;</button>
                </div>
                <div class="modal-body-enhanced" id="ingredient-info-content">
                    <!-- Contenu généré dynamiquement -->
                </div>
            </div>
        </div>';
    }
    
    /**
     * AJAX HANDLERS POUR FONCTIONNALITÉS ENRICHIES
     */
    
    public function track_engagement_ajax() {
        if (!wp_verify_nonce($_POST['nonce'], 'quicky_enhanced_nonce')) {
            wp_send_json_error('Invalid nonce');
        }
        
        $post_id = intval($_POST['post_id']);
        $action_type = sanitize_text_field($_POST['action_type']);
        $user_id = get_current_user_id();
        
        // Tracker l'engagement
        $engagement_data = get_post_meta($post_id, '_quicky_engagement_data', true) ?: [];
        
        if (!isset($engagement_data[$action_type])) {
            $engagement_data[$action_type] = 0;
        }
        
        $engagement_data[$action_type]++;
        $engagement_data['last_interaction'] = current_time('mysql');
        
        update_post_meta($post_id, '_quicky_engagement_data', $engagement_data);
        
        wp_send_json_success($engagement_data);
    }
    
    public function get_nutrition_details_ajax() {
        if (!wp_verify_nonce($_POST['nonce'], 'quicky_enhanced_nonce')) {
            wp_send_json_error('Invalid nonce');
        }
        
        $post_id = intval($_POST['post_id']);
        $servings = intval($_POST['servings']);
        
        $nutrition = json_decode(get_post_meta($post_id, '_quicky_nutrition', true), true);
        
        if ($nutrition) {
            // Calculer nutrition détaillée avec graphiques
            $detailed_nutrition = $this->calculate_detailed_nutrition($nutrition, $servings);
            wp_send_json_success($detailed_nutrition);
        } else {
            wp_send_json_error('Nutrition data not available');
        }
    }
    
    private function calculate_detailed_nutrition($nutrition, $servings) {
        // Calculer les pourcentages de valeurs quotidiennes
        $daily_values = [
            'calories' => 2000,
            'protein' => 50,
            'carbs' => 300,
            'fat' => 65,
            'fiber' => 25
        ];
        
        $detailed = [];
        
        foreach ($nutrition as $key => $value) {
            $numeric_value = intval($value);
            if (isset($daily_values[$key])) {
                $percentage = round(($numeric_value / $daily_values[$key]) * 100);
                $detailed[$key] = [
                    'value' => $numeric_value,
                    'daily_percentage' => $percentage,
                    'recommendation' => $this->get_nutrition_recommendation($key, $percentage)
                ];
            }
        }
        
        return $detailed;
    }
    
    private function get_nutrition_recommendation($nutrient, $percentage) {
        $recommendations = [
            'calories' => $percentage > 30 ? 'High calorie - consider portion size' : 'Moderate calorie content',
            'protein' => $percentage > 40 ? 'Excellent protein source' : 'Good protein content',
            'carbs' => $percentage > 25 ? 'High carb - good for energy' : 'Moderate carb content',
            'fat' => $percentage > 30 ? 'High fat - enjoy in moderation' : 'Healthy fat levels',
            'fiber' => $percentage > 20 ? 'Excellent fiber source' : 'Good fiber content'
        ];
        
        return $recommendations[$nutrient] ?? 'Balanced nutrition';
    }
    
    // Placeholder pour autres templates enrichis
    private function render_enhanced_buying_guide_template() {
        // Implementation pour guide d'achat enrichi
        return $this->render_enhanced_recipe_template(); // Placeholder
    }
    
    private function render_enhanced_comparison_template() {
        // Implementation pour comparatif enrichi
        return $this->render_enhanced_recipe_template(); // Placeholder
    }
    
    private function render_enhanced_blog_template() {
        // Implementation pour blog enrichi
        return $this->render_enhanced_recipe_template(); // Placeholder
    }
    
    // Fonctions utilitaires existantes
    private function get_appliance_icon($appliance_type) {
        $icons = array(
            'air-fryer' => '🍟',
            'instant-pot' => '⚡',
            'slow-cooker' => '🍲',
            'crockpot' => '🍲',
            'toaster-oven' => '🔥',
            'sous-vide' => '🌡️',
            'bread-maker' => '🍞',
            'rice-cooker' => '🍚'
        );
        
        return $icons[$appliance_type] ?? '🍳';
    }
    
    private function get_difficulty_stars($difficulty) {
        $stars = array(
            'easy' => '⭐',
            'medium' => '⭐⭐',
            'hard' => '⭐⭐⭐'
        );
        
        return $stars[$difficulty] ?? '⭐';
    }
}

// Initialisation
new QuickyTemplatesEnhanced();