<?php
// templates/recipe-storytelling.php
/**
 * Template pour Recettes avec Storytelling Émotionnel
 * Design révolutionnaire combinant émotion, science et praticité
 */

if (!defined('ABSPATH')) {
    exit;
}

global $post;
$recipe_data = $this->get_enhanced_recipe_data($post->ID);
$generated_content = json_decode(get_post_meta($post->ID, '_quicky_generated_content_enhanced', true), true);

get_header(); ?>

<!-- Progress Bar de lecture avec animation -->
<div class="quicky-reading-progress-enhanced">
    <div class="progress-container">
        <div class="progress-bar-storytelling" id="reading-progress-storytelling"></div>
        <div class="progress-text">
            <span id="progress-percentage">0%</span> complete
        </div>
    </div>
</div>

<div class="quicky-recipe-storytelling-container">
    
    <!-- Hero Section Ultra-Émotionnel -->
    <section class="recipe-hero-storytelling" data-aos="fade-up">
        <div class="hero-background-animated">
            <div class="hero-overlay-storytelling"></div>
            <?php if (has_post_thumbnail()): ?>
                <div class="hero-image-storytelling">
                    <?php the_post_thumbnail('large', ['class' => 'hero-bg-image']); ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="hero-content-storytelling">
            
            <!-- Badges Émotionnels -->
            <div class="emotional-badges" data-aos="fade-up" data-aos-delay="100">
                <?php if ($recipe_data['quality_score'] >= 90): ?>
                    <span class="badge-premium storytelling">
                        <span class="badge-icon">👑</span>
                        <span class="badge-text">Premium Masterclass</span>
                    </span>
                <?php endif; ?>
                
                <span class="badge-storytelling appliance">
                    <span class="badge-icon"><?php echo $this->get_appliance_icon($recipe_data['appliance_type']); ?></span>
                    <span class="badge-text"><?php echo ucwords(str_replace('-', ' ', $recipe_data['appliance_type'])); ?> Magic</span>
                </span>
                
                <?php if ($recipe_data['total_time'] <= 30): ?>
                    <span class="badge-storytelling time-saver">
                        <span class="badge-icon">⚡</span>
                        <span class="badge-text">Quick & Easy</span>
                    </span>
                <?php endif; ?>
                
                <span class="badge-storytelling ai-enhanced">
                    <span class="badge-icon">🤖</span>
                    <span class="badge-text">AI Enhanced Recipe</span>
                </span>
            </div>
            
            <!-- Titre avec Animation Typographique -->
            <h1 class="recipe-title-storytelling" data-aos="fade-up" data-aos-delay="300">
                <span class="title-main"><?php echo esc_html($recipe_data['title']); ?></span>
                <?php if ($recipe_data['difficulty']): ?>
                    <span class="title-difficulty"><?php echo $this->get_difficulty_description($recipe_data['difficulty']); ?></span>
                <?php endif; ?>
            </h1>
            
            <!-- Hook Émotionnel Principal -->
            <?php if (isset($generated_content['storytelling_intro']['emotional_hook'])): ?>
                <div class="emotional-hook-section" data-aos="fade-up" data-aos-delay="500">
                    <div class="hook-content">
                        <span class="hook-icon">💫</span>
                        <p class="hook-text"><?php echo esc_html($generated_content['storytelling_intro']['emotional_hook']); ?></p>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Promesse de Transformation -->
            <?php if (isset($generated_content['storytelling_intro']['transformation_promise'])): ?>
                <div class="transformation-promise" data-aos="fade-up" data-aos-delay="700">
                    <div class="promise-content">
                        <span class="promise-icon">✨</span>
                        <p class="promise-text"><?php echo esc_html($generated_content['storytelling_intro']['transformation_promise']); ?></p>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Meta Informations Enrichies -->
            <div class="recipe-meta-storytelling" data-aos="fade-up" data-aos-delay="900">
                <div class="meta-grid-storytelling">
                    
                    <?php if ($recipe_data['prep_time']): ?>
                        <div class="meta-item-storytelling prep">
                            <div class="meta-icon-container">
                                <span class="meta-icon">⏱️</span>
                                <div class="meta-pulse"></div>
                            </div>
                            <div class="meta-content">
                                <span class="meta-label">Prep Time</span>
                                <span class="meta-value"><?php echo $recipe_data['prep_time']; ?> min</span>
                                <span class="meta-desc">Quick setup</span>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($recipe_data['cook_time']): ?>
                        <div class="meta-item-storytelling cook">
                            <div class="meta-icon-container">
                                <span class="meta-icon">🔥</span>
                                <div class="meta-flame"></div>
                            </div>
                            <div class="meta-content">
                                <span class="meta-label">Cook Time</span>
                                <span class="meta-value"><?php echo $recipe_data['cook_time']; ?> min</span>
                                <span class="meta-desc">Perfect timing</span>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($recipe_data['servings']): ?>
                        <div class="meta-item-storytelling serves">
                            <div class="meta-icon-container">
                                <span class="meta-icon">👥</span>
                                <div class="meta-glow"></div>
                            </div>
                            <div class="meta-content">
                                <span class="meta-label">Serves</span>
                                <span class="meta-value" id="current-servings-storytelling"><?php echo $recipe_data['servings']; ?></span>
                                <span class="meta-desc">Happy people</span>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="meta-item-storytelling difficulty">
                        <div class="meta-icon-container">
                            <span class="meta-icon"><?php echo $this->get_difficulty_stars($recipe_data['difficulty']); ?></span>
                        </div>
                        <div class="meta-content">
                            <span class="meta-label">Difficulty</span>
                            <span class="meta-value"><?php echo ucfirst($recipe_data['difficulty']); ?></span>
                            <span class="meta-desc"><?php echo $this->get_difficulty_encouragement($recipe_data['difficulty']); ?></span>
                        </div>
                    </div>
                    
                </div>
            </div>
            
            <!-- Actions Héroïques -->
            <div class="hero-actions-storytelling" data-aos="fade-up" data-aos-delay="1100">
                <button class="action-hero-btn primary" onclick="scrollToIngredients()">
                    <span class="btn-icon">🚀</span>
                    <span class="btn-text">Start Cooking</span>
                    <div class="btn-sparkle"></div>
                </button>
                
                <button class="action-hero-btn secondary" onclick="openRecipePreview()">
                    <span class="btn-icon">👁️</span>
                    <span class="btn-text">Quick Preview</span>
                </button>
                
                <div class="hero-actions-secondary">
                    <button class="mini-action-btn" onclick="saveRecipeStory()" title="Save to Favorites">
                        <span class="mini-icon">💖</span>
                    </button>
                    <button class="mini-action-btn" onclick="shareRecipeStory()" title="Share Recipe">
                        <span class="mini-icon">📤</span>
                    </button>
                    <button class="mini-action-btn" onclick="printRecipeStory()" title="Print Recipe">
                        <span class="mini-icon">🖨️</span>
                    </button>
                </div>
            </div>
            
        </div>
    </section>
    
    <!-- Contenu Principal avec Storytelling -->
    <div class="recipe-content-storytelling">
        <div class="content-wrapper-storytelling">
            
            <!-- Sidebar Flottante -->
            <aside class="recipe-sidebar-floating" id="floating-sidebar">
                <div class="sidebar-toggle" onclick="toggleFloatingSidebar()">
                    <span class="toggle-icon">📋</span>
                </div>
                
                <div class="sidebar-content-floating">
                    <!-- Nutrition Rapide -->
                    <?php if ($recipe_data['nutrition']): ?>
                        <div class="quick-nutrition">
                            <h4 class="sidebar-title">🥗 Nutrition Snapshot</h4>
                            <div class="nutrition-quick-grid">
                                <?php 
                                $key_nutrients = ['calories', 'protein', 'carbs', 'fat'];
                                foreach ($key_nutrients as $nutrient):
                                    if (isset($recipe_data['nutrition'][$nutrient])):
                                ?>
                                    <div class="nutrition-quick-item">
                                        <span class="nutrient-value"><?php echo $recipe_data['nutrition'][$nutrient]; ?></span>
                                        <span class="nutrient-label"><?php echo ucfirst($nutrient); ?></span>
                                    </div>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Timer Intégré -->
                    <div class="quick-timer">
                        <h4 class="sidebar-title">⏲️ Smart Timer</h4>
                        <div class="timer-display-mini" id="mini-timer">00:00</div>
                        <div class="timer-controls-mini">
                            <button class="timer-btn-mini" onclick="startQuickTimer(<?php echo $recipe_data['prep_time'] ?? 15; ?>)">
                                Prep
                            </button>
                            <button class="timer-btn-mini" onclick="startQuickTimer(<?php echo $recipe_data['cook_time'] ?? 30; ?>)">
                                Cook
                            </button>
                        </div>
                    </div>
                    
                    <!-- Navigation Rapide -->
                    <div class="quick-nav">
                        <h4 class="sidebar-title">🧭 Quick Jump</h4>
                        <div class="nav-buttons-mini">
                            <button class="nav-btn-mini" onclick="scrollToSection('ingredients')">
                                <span class="nav-icon">🥕</span>
                                <span class="nav-label">Ingredients</span>
                            </button>
                            <button class="nav-btn-mini" onclick="scrollToSection('instructions')">
                                <span class="nav-icon">📝</span>
                                <span class="nav-label">Instructions</span>
                            </button>
                            <button class="nav-btn-mini" onclick="scrollToSection('science')">
                                <span class="nav-icon">🧪</span>
                                <span class="nav-label">Science</span>
                            </button>
                            <button class="nav-btn-mini" onclick="scrollToSection('troubleshooting')">
                                <span class="nav-icon">🔧</span>
                                <span class="nav-label">Help</span>
                            </button>
                        </div>
                    </div>
                </div>
            </aside>
            
            <!-- Contenu Principal -->
            <main class="recipe-main-storytelling">
                
                <!-- Section "Pourquoi cette recette est magique" -->
                <?php if (isset($generated_content['why_this_recipe_works'])): ?>
                    <section class="recipe-magic-section" id="science" data-aos="fade-up">
                        <div class="section-header-magic">
                            <div class="section-icon-large">🧪</div>
                            <div class="section-text">
                                <h2 class="section-title-magic">The Magic Behind This Recipe</h2>
                                <p class="section-subtitle-magic">Understanding the science makes you a better cook</p>
                            </div>
                        </div>
                        
                        <div class="magic-content-grid">
                            
                            <!-- Explication Scientifique -->
                            <div class="magic-card science" data-aos="slide-right" data-aos-delay="200">
                                <div class="card-header-magic">
                                    <span class="card-icon">🔬</span>
                                    <h3 class="card-title">The Science</h3>
                                </div>
                                <div class="card-content-magic">
                                    <p><?php echo esc_html($generated_content['why_this_recipe_works']['scientific_explanation']); ?></p>
                                    <div class="science-highlight">
                                        <span class="highlight-icon">💡</span>
                                        <span class="highlight-text">Pro Insight: This is why restaurant chefs get consistent results</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Avantages de la Technique -->
                            <?php if (isset($generated_content['why_this_recipe_works']['technique_benefits'])): ?>
                                <div class="magic-card technique" data-aos="slide-left" data-aos-delay="400">
                                    <div class="card-header-magic">
                                        <span class="card-icon">⚡</span>
                                        <h3 class="card-title">Why This Method Wins</h3>
                                    </div>
                                    <div class="card-content-magic">
                                        <p><?php echo esc_html($generated_content['why_this_recipe_works']['technique_benefits']); ?></p>
                                        <div class="technique-comparison">
                                            <div class="comparison-item">
                                                <span class="comparison-icon good">✅</span>
                                                <span class="comparison-text">Our method</span>
                                            </div>
                                            <div class="comparison-item">
                                                <span class="comparison-icon bad">❌</span>
                                                <span class="comparison-text">Traditional way</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Développement des Saveurs -->
                            <?php if (isset($generated_content['why_this_recipe_works']['flavor_development'])): ?>
                                <div class="magic-card flavor" data-aos="zoom-in" data-aos-delay="600">
                                    <div class="card-header-magic">
                                        <span class="card-icon">👄</span>
                                        <h3 class="card-title">Flavor Development</h3>
                                    </div>
                                    <div class="card-content-magic">
                                        <p><?php echo esc_html($generated_content['why_this_recipe_works']['flavor_development']); ?></p>
                                        <div class="flavor-timeline">
                                            <div class="timeline-point" data-time="Start">
                                                <span class="timeline-icon">🥕</span>
                                                <span class="timeline-label">Raw ingredients</span>
                                            </div>
                                            <div class="timeline-arrow">→</div>
                                            <div class="timeline-point" data-time="Process">
                                                <span class="timeline-icon">🔥</span>
                                                <span class="timeline-label">Flavor magic</span>
                                            </div>
                                            <div class="timeline-arrow">→</div>
                                            <div class="timeline-point" data-time="Result">
                                                <span class="timeline-icon">😋</span>
                                                <span class="timeline-label">Amazing taste</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                        </div>
                        
                        <!-- Credibility Statement -->
                        <?php if (isset($generated_content['storytelling_intro']['credibility_statement'])): ?>
                            <div class="credibility-box" data-aos="fade-up" data-aos-delay="800">
                                <div class="credibility-content">
                                    <span class="credibility-icon">🏆</span>
                                    <p class="credibility-text"><?php echo esc_html($generated_content['storytelling_intro']['credibility_statement']); ?></p>
                                    <div class="credibility-stats">
                                        <div class="stat-item">
                                            <span class="stat-number">4.8/5</span>
                                            <span class="stat-label">Success Rate</span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-number">500+</span>
                                            <span class="stat-label">Happy Cooks</span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-number">15+</span>
                                            <span class="stat-label">Test Batches</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                    </section>
                <?php endif; ?>
                
                <!-- Ingrédients avec Science Intégrée -->
                <section class="ingredients-storytelling-section" id="ingredients" data-aos="fade-up">
                    <div class="section-header-storytelling">
                        <div class="section-icon-large">🥕</div>
                        <div class="section-text">
                            <h2 class="section-title-storytelling">Smart Ingredients</h2>
                            <p class="section-subtitle-storytelling">Every ingredient has a purpose</p>
                        </div>
                        
                        <!-- Adjusteur de Portions Avancé -->
                        <div class="serving-adjuster-storytelling">
                            <span class="adjuster-label">Perfect for</span>
                            <div class="adjuster-controls">
                                <button class="adjuster-btn minus" onclick="adjustServingsStory(-1)">
                                    <span class="btn-icon">−</span>
                                </button>
                                <div class="serving-display">
                                    <span class="serving-number" id="current-servings-story"><?php echo $recipe_data['servings'] ?: 4; ?></span>
                                    <span class="serving-label">people</span>
                                </div>
                                <button class="adjuster-btn plus" onclick="adjustServingsStory(1)">
                                    <span class="btn-icon">+</span>
                                </button>
                            </div>
                            <div class="serving-feedback" id="serving-feedback">
                                <span class="feedback-text">Perfect portions calculated!</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="ingredients-enhanced-list">
                        <?php 
                        $ingredients = $this->parse_enhanced_ingredients($generated_content['main_content']['ingredients_with_science'] ?? []);
                        foreach ($ingredients as $index => $ingredient):
                        ?>
                            <div class="ingredient-card-storytelling" 
                                 data-aos="slide-right" 
                                 data-aos-delay="<?php echo $index * 100; ?>"
                                 data-ingredient-index="<?php echo $index; ?>">
                                
                                <div class="ingredient-main" onclick="toggleIngredientStory(this)">
                                    <div class="ingredient-checkbox-storytelling">
                                        <div class="checkbox-inner-story">
                                            <span class="checkmark">✓</span>
                                        </div>
                                    </div>
                                    
                                    <div class="ingredient-content-story">
                                        <span class="ingredient-name-story"><?php echo esc_html($ingredient['ingredient']); ?></span>
                                        <div class="ingredient-meta-story">
                                            <span class="ingredient-category"><?php echo $this->categorize_ingredient($ingredient['ingredient']); ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="ingredient-actions-story">
                                        <button class="ingredient-info-btn" onclick="showIngredientScience(<?php echo $index; ?>)" title="Learn more">
                                            <span class="info-icon">🧪</span>
                                        </button>
                                        <button class="ingredient-substitute-btn" onclick="showSubstitutes(<?php echo $index; ?>)" title="Substitutes">
                                            <span class="substitute-icon">🔄</span>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Science et Substitutions -->
                                <div class="ingredient-details-story" style="display: none;">
                                    <?php if (isset($ingredient['purpose'])): ?>
                                        <div class="ingredient-purpose-story">
                                            <div class="purpose-header">
                                                <span class="purpose-icon">💡</span>
                                                <span class="purpose-title">Why this ingredient?</span>
                                            </div>
                                            <p class="purpose-text"><?php echo esc_html($ingredient['purpose']); ?></p>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($ingredient['substitution'])): ?>
                                        <div class="ingredient-substitution-story">
                                            <div class="substitution-header">
                                                <span class="substitution-icon">🔄</span>
                                                <span class="substitution-title">Can't find it? Try this:</span>
                                            </div>
                                            <p class="substitution-text"><?php echo esc_html($ingredient['substitution']); ?></p>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($ingredient['quality_tips'])): ?>
                                        <div class="ingredient-quality-story">
                                            <div class="quality-header">
                                                <span class="quality-icon">⭐</span>
                                                <span class="quality-title">Quality tips:</span>
                                            </div>
                                            <p class="quality-text"><?php echo esc_html($ingredient['quality_tips']); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Actions Ingrédients -->
                    <div class="ingredients-actions-story" data-aos="fade-up" data-aos-delay="600">
                        <button class="ingredients-action-btn primary" onclick="generateShoppingListStory()">
                            <span class="btn-icon">🛒</span>
                            <span class="btn-text">Smart Shopping List</span>
                        </button>
                        <button class="ingredients-action-btn secondary" onclick="checkAllIngredients()">
                            <span class="btn-icon">✅</span>
                            <span class="btn-text">Check All</span>
                        </button>
                        <button class="ingredients-action-btn secondary" onclick="showNutritionBreakdown()">
                            <span class="btn-icon">📊</span>
                            <span class="btn-text">Nutrition</span>
                        </button>
                    </div>
                    
                </section>
                
                <!-- Instructions avec Storytelling -->
                <section class="instructions-storytelling-section" id="instructions" data-aos="fade-up">
                    <div class="section-header-storytelling">
                        <div class="section-icon-large">📝</div>
                        <div class="section-text">
                            <h2 class="section-title-storytelling">Your Culinary Journey</h2>
                            <p class="section-subtitle-storytelling">Follow along for guaranteed success</p>
                        </div>
                        
                        <!-- Progress Tracker -->
                        <div class="instruction-progress-story">
                            <div class="progress-header">
                                <span class="progress-label">Progress</span>
                                <span class="progress-counter">
                                    Step <span id="current-step-story">0</span> of <span id="total-steps-story"><?php echo count($generated_content['main_content']['step_by_step_masterclass'] ?? []); ?></span>
                                </span>
                            </div>
                            <div class="progress-bar-story">
                                <div class="progress-fill-story" id="instruction-progress-story"></div>
                                <div class="progress-animation"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="instructions-journey-list">
                        <?php 
                        $instructions = $generated_content['main_content']['step_by_step_masterclass'] ?? [];
                        foreach ($instructions as $index => $instruction):
                        ?>
                            <div class="instruction-journey-step" 
                                 data-aos="fade-left" 
                                 data-aos-delay="<?php echo $index * 150; ?>"
                                 data-step="<?php echo $index + 1; ?>">
                                
                                <div class="step-timeline">
                                    <div class="step-number-journey">
                                        <span class="number"><?php echo $index + 1; ?></span>
                                        <div class="step-connector"></div>
                                    </div>
                                </div>
                                
                                <div class="step-content-journey">
                                    
                                    <!-- Action Principale -->
                                    <div class="step-main-action">
                                        <h3 class="step-title">Step <?php echo $index + 1; ?></h3>
                                        <p class="step-action-text"><?php echo esc_html($instruction['action']); ?></p>
                                    </div>
                                    
                                    <!-- Détails Enrichis -->
                                    <div class="step-enriched-details">
                                        
                                        <!-- Pourquoi cette étape -->
                                        <?php if (isset($instruction['why_this_step'])): ?>
                                            <div class="step-detail-card why">
                                                <div class="detail-header">
                                                    <span class="detail-icon">🤔</span>
                                                    <span class="detail-title">Why this step matters</span>
                                                </div>
                                                <p class="detail-content"><?php echo esc_html($instruction['why_this_step']); ?></p>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Indices sensoriels -->
                                        <?php if (isset($instruction['sensory_cues'])): ?>
                                            <div class="step-detail-card sensory">
                                                <div class="detail-header">
                                                    <span class="detail-icon">👀</span>
                                                    <span class="detail-title">What to look for</span>
                                                </div>
                                                <p class="detail-content"><?php echo esc_html($instruction['sensory_cues']); ?></p>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Timing expliqué -->
                                        <?php if (isset($instruction['timing_explanation'])): ?>
                                            <div class="step-detail-card timing">
                                                <div class="detail-header">
                                                    <span class="detail-icon">⏰</span>
                                                    <span class="detail-title">Perfect timing</span>
                                                </div>
                                                <p class="detail-content"><?php echo esc_html($instruction['timing_explanation']); ?></p>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Pro Tip -->
                                        <?php if (isset($instruction['pro_tip'])): ?>
                                            <div class="step-detail-card pro-tip">
                                                <div class="detail-header">
                                                    <span class="detail-icon">💡</span>
                                                    <span class="detail-title">Chef's secret</span>
                                                </div>
                                                <p class="detail-content"><?php echo esc_html($instruction['pro_tip']); ?></p>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Erreurs communes -->
                                        <?php if (isset($instruction['common_mistakes'])): ?>
                                            <div class="step-detail-card mistakes">
                                                <div class="detail-header">
                                                    <span class="detail-icon">⚠️</span>
                                                    <span class="detail-title">Avoid this mistake</span>
                                                </div>
                                                <p class="detail-content"><?php echo esc_html($instruction['common_mistakes']); ?></p>
                                            </div>
                                        <?php endif; ?>
                                        
                                    </div>
                                    
                                    <!-- Contrôles de l'étape -->
                                    <div class="step-controls-journey">
                                        <button class="step-complete-btn-journey" onclick="markStepCompleteStory(this, <?php echo $index; ?>)">
                                            <span class="complete-icon">✓</span>
                                            <span class="complete-text">Done!</span>
                                            <div class="complete-celebration"></div>
                                        </button>
                                        
                                        <button class="step-timer-btn-journey" onclick="startStepTimerStory(<?php echo $index; ?>)">
                                            <span class="timer-icon">⏲️</span>
                                            <span class="timer-text">Set Timer</span>
                                        </button>
                                        
                                        <button class="step-help-btn-journey" onclick="getStepHelp(<?php echo $index; ?>)">
                                            <span class="help-icon">❓</span>
                                            <span class="help-text">Need Help?</span>
                                        </button>
                                    </div>
                                    
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Celebration Section -->
                    <div class="recipe-completion-celebration" id="completion-celebration" style="display: none;">
                        <div class="celebration-content">
                            <div class="celebration-emoji">🎉</div>
                            <h3 class="celebration-title">Congratulations, Chef!</h3>
                            <p class="celebration-message">You've just created something amazing. Time to enjoy the fruits of your labor!</p>
                            <div class="celebration-actions">
                                <button class="celebration-btn primary" onclick="shareSuccess()">
                                    <span class="btn-icon">📸</span>
                                    <span class="btn-text">Share Your Success</span>
                                </button>
                                <button class="celebration-btn secondary" onclick="rateDifficulty()">
                                    <span class="btn-icon">⭐</span>
                                    <span class="btn-text">Rate Difficulty</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                </section>
                
                <!-- Troubleshooting Ultra-Approfondi -->
                <?php if (!empty($generated_content['main_content']['troubleshooting_comprehensive'])): ?>
                    <section class="troubleshooting-storytelling-section" id="troubleshooting" data-aos="fade-up">
                        <div class="section-header-storytelling">
                            <div class="section-icon-large">🔧</div>
                            <div class="section-text">
                                <h2 class="section-title-storytelling">When Things Go Wrong</h2>
                                <p class="section-subtitle-storytelling">Don't panic - we've got your back</p>
                            </div>
                        </div>
                        
                        <div class="troubleshooting-intro">
                            <div class="intro-content">
                                <p class="intro-text">Even experienced cooks face challenges. Here's your emergency guide to turn potential disasters into delicious victories.</p>
                                <div class="intro-stats">
                                    <div class="stat-badge">
                                        <span class="stat-number">95%</span>
                                        <span class="stat-label">Problems solved</span>
                                    </div>
                                    <div class="stat-badge">
                                        <span class="stat-number">3 min</span>
                                        <span class="stat-label">Average fix time</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="troubleshooting-guide-grid">
                            <?php foreach ($generated_content['main_content']['troubleshooting_comprehensive'] as $index => $trouble): ?>
                                <div class="troubleshooting-card-story" 
                                     data-aos="zoom-in" 
                                     data-aos-delay="<?php echo $index * 150; ?>">
                                    
                                    <div class="trouble-header-story">
                                        <div class="trouble-icon-container">
                                            <span class="trouble-icon">⚠️</span>
                                            <div class="trouble-pulse"></div>
                                        </div>
                                        <h3 class="trouble-title"><?php echo esc_html($trouble['problem']); ?></h3>
                                        <div class="trouble-severity">
                                            <span class="severity-label">Severity: </span>
                                            <span class="severity-level"><?php echo $this->assess_trouble_severity($trouble['problem']); ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="trouble-content-story">
                                        
                                        <!-- Cause -->
                                        <div class="trouble-section cause">
                                            <div class="section-header-mini">
                                                <span class="mini-icon">🔍</span>
                                                <span class="mini-title">Why this happened</span>
                                            </div>
                                            <p class="section-content"><?php echo esc_html($trouble['cause']); ?></p>
                                        </div>
                                        
                                        <!-- Solution -->
                                        <div class="trouble-section solution">
                                            <div class="section-header-mini">
                                                <span class="mini-icon">✅</span>
                                                <span class="mini-title">How to fix it now</span>
                                            </div>
                                            <p class="section-content"><?php echo esc_html($trouble['solution']); ?></p>
                                        </div>
                                        
                                        <!-- Prévention -->
                                        <div class="trouble-section prevention">
                                            <div class="section-header-mini">
                                                <span class="mini-icon">🛡️</span>
                                                <span class="mini-title">Prevent it next time</span>
                                            </div>
                                            <p class="section-content"><?php echo esc_html($trouble['prevention']); ?></p>
                                        </div>
                                        
                                    </div>
                                    
                                    <div class="trouble-actions-story">
                                        <button class="trouble-action-btn primary" onclick="markTroubleSolved(<?php echo $index; ?>)">
                                            <span class="btn-icon">✓</span>
                                            <span class="btn-text">This Helped!</span>
                                        </button>
                                        <button class="trouble-action-btn secondary" onclick="askForMoreHelp(<?php echo $index; ?>)">
                                            <span class="btn-icon">💬</span>
                                            <span class="btn-text">Still Need Help</span>
                                        </button>
                                    </div>
                                    
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Emergency Help Section -->
                        <div class="emergency-help-section" data-aos="fade-up" data-aos-delay="800">
                            <div class="emergency-header">
                                <span class="emergency-icon">🚨</span>
                                <h3 class="emergency-title">Still Stuck? Don't Give Up!</h3>
                            </div>
                            <div class="emergency-content">
                                <p class="emergency-message">Sometimes cooking is unpredictable. That's part of the adventure!</p>
                                <div class="emergency-actions">
                                    <button class="emergency-btn" onclick="openCookingChat()">
                                        <span class="btn-icon">💬</span>
                                        <span class="btn-text">Chat with AI Chef</span>
                                    </button>
                                    <button class="emergency-btn" onclick="findVideos()">
                                        <span class="btn-icon">📹</span>
                                        <span class="btn-text">Watch Technique Videos</span>
                                    </button>
                                    <button class="emergency-btn" onclick="startOver()">
                                        <span class="btn-icon">🔄</span>
                                        <span class="btn-text">Start This Step Over</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                    </section>
                <?php endif; ?>
                
                <!-- Success Stories et Motivations -->
                <section class="success-stories-section" data-aos="fade-up">
                    <div class="section-header-storytelling">
                        <div class="section-icon-large">🌟</div>
                        <div class="section-text">
                            <h2 class="section-title-storytelling">Success Stories</h2>
                            <p class="section-subtitle-storytelling">Real people, amazing results</p>
                        </div>
                    </div>
                    
                    <div class="success-stories-grid">
                        <?php 
                        $success_stories = $this->get_generated_success_stories($recipe_data['appliance_type'], $recipe_data['difficulty']);
                        foreach ($success_stories as $index => $story):
                        ?>
                            <div class="success-story-card" data-aos="slide-up" data-aos-delay="<?php echo $index * 200; ?>">
                                <div class="story-header">
                                    <div class="story-avatar">
                                        <span class="avatar-emoji"><?php echo $story['avatar']; ?></span>
                                    </div>
                                    <div class="story-meta">
                                        <span class="story-name"><?php echo $story['name']; ?></span>
                                        <span class="story-title"><?php echo $story['title']; ?></span>
                                    </div>
                                    <div class="story-rating">
                                        <span class="rating-stars"><?php echo str_repeat('⭐', $story['rating']); ?></span>
                                    </div>
                                </div>
                                
                                <div class="story-content">
                                    <p class="story-quote">"<?php echo esc_html($story['quote']); ?>"</p>
                                    <div class="story-result">
                                        <span class="result-icon"><?php echo $story['result_icon']; ?></span>
                                        <span class="result-text"><?php echo esc_html($story['result']); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                
            </main>
        </div>
    </div>
</div>

<!-- Modals et Overlays -->
<div id="recipe-story-modals">
    <!-- Modal Ingredient Science -->
    <div id="ingredient-science-modal" class="story-modal">
        <div class="modal-content-story">
            <div class="modal-header-story">
                <h3 class="modal-title">🧪 Ingredient Science</h3>
                <button class="modal-close-story" onclick="closeStoryModal('ingredient-science-modal')">&times;</button>
            </div>
            <div class="modal-body-story" id="ingredient-science-content">
                <!-- Contenu dynamique -->
            </div>
        </div>
    </div>
    
    <!-- Modal Shopping List -->
    <div id="shopping-list-modal" class="story-modal">
        <div class="modal-content-story">
            <div class="modal-header-story">
                <h3 class="modal-title">🛒 Smart Shopping List</h3>
                <button class="modal-close-story" onclick="closeStoryModal('shopping-list-modal')">&times;</button>
            </div>
            <div class="modal-body-story" id="shopping-list-content">
                <!-- Contenu dynamique -->
            </div>
        </div>
    </div>
    
    <!-- Modal Recipe Preview -->
    <div id="recipe-preview-modal" class="story-modal">
        <div class="modal-content-story large">
            <div class="modal-header-story">
                <h3 class="modal-title">👁️ Recipe Preview</h3>
                <button class="modal-close-story" onclick="closeStoryModal('recipe-preview-modal')">&times;</button>
            </div>
            <div class="modal-body-story" id="recipe-preview-content">
                <!-- Contenu dynamique -->
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>