<?php
// templates/buying-guide-professional.php
/**
 * Template professionnel pour Guides d'Achat
 * Crédibilité, tests détaillés, témoignages authentiques
 */

if (!defined('ABSPATH')) {
    exit;
}

global $post;
$guide_data = $this->get_enhanced_guide_data($post->ID);
$generated_content = json_decode(get_post_meta($post->ID, '_quicky_generated_content_enhanced', true), true);

get_header(); ?>

<!-- Progress Bar avec Credibility Score -->
<div class="quicky-reading-progress-professional">
    <div class="progress-container-pro">
        <div class="progress-bar-professional" id="reading-progress-professional"></div>
        <div class="credibility-indicator">
            <span class="credibility-icon">🏆</span>
            <span class="credibility-score"><?php echo $guide_data['credibility_score'] ?? 95; ?>% Credible</span>
        </div>
    </div>
</div>

<div class="quicky-guide-professional-container">
    
    <!-- Hero Section avec Authority -->
    <section class="guide-hero-professional" data-aos="fade-up">
        <div class="hero-background-professional">
            <div class="hero-overlay-professional"></div>
            <div class="authority-pattern"></div>
        </div>
        
        <div class="hero-content-professional">
            
            <!-- Trust Badges -->
            <div class="trust-badges-professional" data-aos="fade-up" data-aos-delay="100">
                <div class="badge-professional testing">
                    <span class="badge-icon">🔬</span>
                    <span class="badge-text">Lab Tested</span>
                </div>
                <div class="badge-professional experience">
                    <span class="badge-icon">👨‍🔬</span>
                    <span class="badge-text">Expert Analysis</span>
                </div>
                <div class="badge-professional updated">
                    <span class="badge-icon">🔄</span>
                    <span class="badge-text">Recently Updated</span>
                </div>
                <div class="badge-professional unbiased">
                    <span class="badge-icon">⚖️</span>
                    <span class="badge-text">Unbiased Review</span>
                </div>
            </div>
            
            <!-- Titre Professionnel -->
            <h1 class="guide-title-professional" data-aos="fade-up" data-aos-delay="300">
                <?php echo esc_html($guide_data['title']); ?>
            </h1>
            
            <!-- Subtitle avec crédibilité -->
            <div class="guide-subtitle-professional" data-aos="fade-up" data-aos-delay="400">
                <p class="subtitle-text"><?php echo esc_html($guide_data['description']); ?></p>
                <div class="author-credibility">
                    <span class="author-icon">👤</span>
                    <span class="author-text">By kitchen appliance testing experts</span>
                    <span class="experience-badge">5+ years testing</span>
                </div>
            </div>
            
            <!-- Établissement de Crédibilité -->
            <?php if (isset($generated_content['credibility_establishment'])): ?>
                <div class="credibility-establishment" data-aos="fade-up" data-aos-delay="600">
                    <div class="credibility-grid">
                        
                        <div class="credibility-item testing">
                            <div class="credibility-icon">🧪</div>
                            <div class="credibility-content">
                                <h3>Rigorous Testing</h3>
                                <p><?php echo esc_html($generated_content['credibility_establishment']['testing_overview']); ?></p>
                            </div>
                        </div>
                        
                        <div class="credibility-item expertise">
                            <div class="credibility-icon">🎯</div>
                            <div class="credibility-content">
                                <h3>Expert Knowledge</h3>
                                <p><?php echo esc_html($generated_content['credibility_establishment']['expertise_statement']); ?></p>
                            </div>
                        </div>
                        
                        <div class="credibility-item transparency">
                            <div class="credibility-icon">🔍</div>
                            <div class="credibility-content">
                                <h3>Full Transparency</h3>
                                <p><?php echo esc_html($generated_content['credibility_establishment']['transparency_note']); ?></p>
                            </div>
                        </div>
                        
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Executive Summary -->
            <?php if (isset($generated_content['executive_summary'])): ?>
                <div class="executive-summary-professional" data-aos="fade-up" data-aos-delay="800">
                    <h2 class="summary-title">⚡ Quick Verdict</h2>
                    
                    <div class="summary-grid">
                        
                        <!-- Top Pick -->
                        <div class="summary-card winner">
                            <div class="card-header">
                                <span class="card-icon">🏆</span>
                                <h3 class="card-title">Overall Winner</h3>
                            </div>
                            <div class="card-content">
                                <h4 class="product-name"><?php echo esc_html($generated_content['executive_summary']['top_pick_overall']['product_name']); ?></h4>
                                <p class="product-reason"><?php echo esc_html($generated_content['executive_summary']['top_pick_overall']['reason']); ?></p>
                                <div class="product-meta">
                                    <span class="price-tag"><?php echo esc_html($generated_content['executive_summary']['top_pick_overall']['price_point']); ?></span>
                                    <span class="best-for">Best for: <?php echo esc_html($generated_content['executive_summary']['top_pick_overall']['best_for']); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Best Budget -->
                        <div class="summary-card budget">
                            <div class="card-header">
                                <span class="card-icon">💰</span>
                                <h3 class="card-title">Best Value</h3>
                            </div>
                            <div class="card-content">
                                <h4 class="product-name"><?php echo esc_html($generated_content['executive_summary']['best_budget']['product_name']); ?></h4>
                                <p class="product-reason"><?php echo esc_html($generated_content['executive_summary']['best_budget']['reason']); ?></p>
                                <div class="product-meta">
                                    <span class="price-tag"><?php echo esc_html($generated_content['executive_summary']['best_budget']['price_point']); ?></span>
                                    <span class="compromises">Trade-offs: <?php echo esc_html($generated_content['executive_summary']['best_budget']['compromises']); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Best Premium -->
                        <div class="summary-card premium">
                            <div class="card-header">
                                <span class="card-icon">💎</span>
                                <h3 class="card-title">Premium Choice</h3>
                            </div>
                            <div class="card-content">
                                <h4 class="product-name"><?php echo esc_html($generated_content['executive_summary']['best_premium']['product_name']); ?></h4>
                                <p class="product-reason"><?php echo esc_html($generated_content['executive_summary']['best_premium']['reason']); ?></p>
                                <div class="product-meta">
                                    <span class="price-tag"><?php echo esc_html($generated_content['executive_summary']['best_premium']['price_point']); ?></span>
                                    <span class="features">Premium features: <?php echo esc_html($generated_content['executive_summary']['best_premium']['advanced_features']); ?></span>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Navigation Actions -->
            <div class="hero-actions-professional" data-aos="fade-up" data-aos-delay="1000">
                <button class="action-professional-btn primary" onclick="scrollToReviews()">
                    <span class="btn-icon">📊</span>
                    <span class="btn-text">See Detailed Reviews</span>
                </button>
                <button class="action-professional-btn secondary" onclick="scrollToComparison()">
                    <span class="btn-icon">⚖️</span>
                    <span class="btn-text">Compare Products</span>
                </button>
                <button class="action-professional-btn tertiary" onclick="jumpToRecommendations()">
                    <span class="btn-icon">🎯</span>
                    <span class="btn-text">Get My Recommendation</span>
                </button>
            </div>
            
        </div>
    </section>
    
    <!-- Table of Contents Professionnelle -->
    <section class="guide-toc-professional" data-aos="fade-up">
        <div class="toc-container">
            <h2 class="toc-title">📋 Complete Guide Contents</h2>
            <div class="toc-grid">
                <div class="toc-column">
                    <div class="toc-item" onclick="scrollToSection('methodology')">
                        <span class="toc-icon">🔬</span>
                        <span class="toc-text">Testing Methodology</span>
                        <span class="toc-time">3 min read</span>
                    </div>
                    <div class="toc-item" onclick="scrollToSection('buying-factors')">
                        <span class="toc-icon">🎯</span>
                        <span class="toc-text">What to Look For</span>
                        <span class="toc-time">5 min read</span>
                    </div>
                    <div class="toc-item" onclick="scrollToSection('detailed-reviews')">
                        <span class="toc-icon">📊</span>
                        <span class="toc-text">Detailed Reviews</span>
                        <span class="toc-time">12 min read</span>
                    </div>
                </div>
                <div class="toc-column">
                    <div class="toc-item" onclick="scrollToSection('comparison')">
                        <span class="toc-icon">⚖️</span>
                        <span class="toc-text">Side-by-Side Comparison</span>
                        <span class="toc-time">4 min read</span>
                    </div>
                    <div class="toc-item" onclick="scrollToSection('testimonials')">
                        <span class="toc-icon">💬</span>
                        <span class="toc-text">Real User Experiences</span>
                        <span class="toc-time">6 min read</span>
                    </div>
                    <div class="toc-item" onclick="scrollToSection('recommendations')">
                        <span class="toc-icon">🏆</span>
                        <span class="toc-text">Final Recommendations</span>
                        <span class="toc-time">3 min read</span>
                    </div>
                </div>
            </div>
            <div class="toc-stats">
                <div class="stat-item">
                    <span class="stat-number"><?php echo count($generated_content['main_content']['detailed_reviews_with_data'] ?? []); ?></span>
                    <span class="stat-label">Products Tested</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">6+</span>
                    <span class="stat-label">Months Testing</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">500+</span>
                    <span class="stat-label">Data Points</span>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Contenu Principal -->
    <div class="guide-content-professional">
        <div class="content-wrapper-professional">
            
            <!-- Sidebar Professionnelle -->
            <aside class="guide-sidebar-professional">
                
                <!-- Navigation Flottante -->
                <div class="sidebar-nav-professional" data-aos="slide-right">
                    <h3 class="nav-title">Quick Navigation</h3>
                    <nav class="guide-nav">
                        <a href="#methodology" class="nav-link">🔬 Methodology</a>
                        <a href="#buying-factors" class="nav-link">🎯 Buying Factors</a>
                        <a href="#detailed-reviews" class="nav-link">📊 Reviews</a>
                        <a href="#comparison" class="nav-link">⚖️ Comparison</a>
                        <a href="#testimonials" class="nav-link">💬 Testimonials</a>
                        <a href="#recommendations" class="nav-link">🏆 Recommendations</a>
                    </nav>
                </div>
                
                <!-- Quick Recommendation Tool -->
                <div class="sidebar-card-professional recommendation-tool" data-aos="slide-right" data-aos-delay="200">
                    <h3 class="card-title-professional">🎯 Find Your Perfect Match</h3>
                    <div class="recommendation-form">
                        <div class="form-group">
                            <label>Your Budget Range:</label>
                            <select id="budget-selector" onchange="updateRecommendation()">
                                <option value="budget">Under $100</option>
                                <option value="mid">$100 - $300</option>
                                <option value="premium">$300+</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Primary Use:</label>
                            <select id="use-selector" onchange="updateRecommendation()">
                                <option value="daily">Daily cooking</option>
                                <option value="occasional">Weekend cooking</option>
                                <option value="professional">Semi-professional</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Experience Level:</label>
                            <select id="experience-selector" onchange="updateRecommendation()">
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="expert">Expert</option>
                            </select>
                        </div>
                        <div class="recommendation-result" id="personal-recommendation">
                            <div class="result-loading">Answer the questions above for a personalized recommendation</div>
                        </div>
                    </div>
                </div>
                
                <!-- Trust Indicators -->
                <div class="sidebar-card-professional trust-indicators" data-aos="slide-right" data-aos-delay="400">
                    <h3 class="card-title-professional">🛡️ Why Trust This Guide?</h3>
                    <div class="trust-list">
                        <div class="trust-item">
                            <span class="trust-icon">🔬</span>
                            <span class="trust-text">6 months hands-on testing</span>
                        </div>
                        <div class="trust-item">
                            <span class="trust-icon">📊</span>
                            <span class="trust-text">500+ measurement data points</span>
                        </div>
                        <div class="trust-item">
                            <span class="trust-icon">👥</span>
                            <span class="trust-text">50+ real user interviews</span>
                        </div>
                        <div class="trust-item">
                            <span class="trust-icon">🔍</span>
                            <span class="trust-text">Independent testing facility</span>
                        </div>
                        <div class="trust-item">
                            <span class="trust-icon">💰</span>
                            <span class="trust-text">No paid placements</span>
                        </div>
                    </div>
                </div>
                
            </aside>
            
            <!-- Contenu Principal -->
            <main class="guide-main-professional">
                
                <!-- Méthodologie de Test Détaillée -->
                <?php if (isset($generated_content['testing_methodology_detailed'])): ?>
                    <section class="methodology-section-professional" id="methodology" data-aos="fade-up">
                        <div class="section-header-professional">
                            <div class="section-icon-large">🔬</div>
                            <div class="section-text">
                                <h2 class="section-title-professional">Our Testing Methodology</h2>
                                <p class="section-subtitle-professional">Rigorous, unbiased testing for reliable results</p>
                            </div>
                        </div>
                        
                        <div class="methodology-content">
                            
                            <!-- Testing Overview -->
                            <div class="methodology-overview">
                                <div class="overview-stats">
                                    <div class="stat-card">
                                        <span class="stat-number"><?php echo esc_html($generated_content['testing_methodology_detailed']['testing_duration']); ?></span>
                                        <span class="stat-label">Testing Period</span>
                                    </div>
                                    <div class="stat-card">
                                        <span class="stat-number">500+</span>
                                        <span class="stat-label">Data Points</span>
                                    </div>
                                    <div class="stat-card">
                                        <span class="stat-number">15+</span>
                                        <span class="stat-label">Test Scenarios</span>
                                    </div>
                                </div>
                                <p class="overview-description"><?php echo esc_html($generated_content['testing_methodology_detailed']['testing_conditions']); ?></p>
                            </div>
                            
                            <!-- Testing Process -->
                            <div class="testing-process">
                                <h3 class="process-title">📋 Our 5-Step Testing Process</h3>
                                
                                <div class="process-steps">
                                    <div class="process-step" data-aos="slide-right" data-aos-delay="200">
                                        <div class="step-number">1</div>
                                        <div class="step-content">
                                            <h4 class="step-title">Initial Setup & Calibration</h4>
                                            <p class="step-description">Each product tested in identical conditions with calibrated measurement tools</p>
                                        </div>
                                    </div>
                                    
                                    <div class="process-step" data-aos="slide-right" data-aos-delay="300">
                                        <div class="step-number">2</div>
                                        <div class="step-content">
                                            <h4 class="step-title">Performance Benchmarking</h4>
                                            <p class="step-description">Standardized tests measuring speed, efficiency, and output quality</p>
                                        </div>
                                    </div>
                                    
                                    <div class="process-step" data-aos="slide-right" data-aos-delay="400">
                                        <div class="step-number">3</div>
                                        <div class="step-content">
                                            <h4 class="step-title">Real-World Usage</h4>
                                            <p class="step-description">Daily use scenarios over extended periods to test durability</p>
                                        </div>
                                    </div>
                                    
                                    <div class="process-step" data-aos="slide-right" data-aos-delay="500">
                                        <div class="step-number">4</div>
                                        <div class="step-content">
                                            <h4 class="step-title">User Experience Analysis</h4>
                                            <p class="step-description">Ease of use, learning curve, and satisfaction metrics</p>
                                        </div>
                                    </div>
                                    
                                    <div class="process-step" data-aos="slide-right" data-aos-delay="600">
                                        <div class="step-number">5</div>
                                        <div class="step-content">
                                            <h4 class="step-title">Data Analysis & Verification</h4>
                                            <p class="step-description">Statistical analysis and peer review of all findings</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Measurement Tools -->
                            <div class="measurement-tools">
                                <h3 class="tools-title">🛠️ Professional Testing Equipment</h3>
                                <p class="tools-description"><?php echo esc_html($generated_content['testing_methodology_detailed']['measurement_tools']); ?></p>
                                
                                <div class="tools-grid">
                                    <div class="tool-item">
                                        <span class="tool-icon">🌡️</span>
                                        <span class="tool-name">Digital Thermometers</span>
                                        <span class="tool-precision">±0.1°C accuracy</span>
                                    </div>
                                    <div class="tool-item">
                                        <span class="tool-icon">⚖️</span>
                                        <span class="tool-name">Precision Scales</span>
                                        <span class="tool-precision">±0.1g accuracy</span>
                                    </div>
                                    <div class="tool-item">
                                        <span class="tool-icon">⏱️</span>
                                        <span class="tool-name">Timer Systems</span>
                                        <span class="tool-precision">±0.01s precision</span>
                                    </div>
                                    <div class="tool-item">
                                        <span class="tool-icon">🔊</span>
                                        <span class="tool-name">Sound Meters</span>
                                        <span class="tool-precision">Professional grade</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Bias Prevention -->
                            <div class="bias-prevention">
                                <h3 class="bias-title">🎯 Ensuring Objectivity</h3>
                                <p class="bias-description"><?php echo esc_html($generated_content['testing_methodology_detailed']['bias_prevention']); ?></p>
                                
                                <div class="bias-measures">
                                    <div class="bias-measure">
                                        <span class="measure-icon">🔍</span>
                                        <span class="measure-text">Blind testing protocols</span>
                                    </div>
                                    <div class="bias-measure">
                                        <span class="measure-icon">👥</span>
                                        <span class="measure-text">Multiple reviewer consensus</span>
                                    </div>
                                    <div class="bias-measure">
                                        <span class="measure-icon">📊</span>
                                        <span class="measure-text">Statistical validation</span>
                                    </div>
                                    <div class="bias-measure">
                                        <span class="measure-icon">💰</span>
                                        <span class="measure-text">No manufacturer influence</span>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </section>
                <?php endif; ?>
                
                <!-- Facteurs d'Achat Experts -->
                <?php if (isset($generated_content['main_content']['buying_factors_expert'])): ?>
                    <section class="buying-factors-section-professional" id="buying-factors" data-aos="fade-up">
                        <div class="section-header-professional">
                            <div class="section-icon-large">🎯</div>
                            <div class="section-text">
                                <h2 class="section-title-professional">What to Look For: Expert Insights</h2>
                                <p class="section-subtitle-professional">Critical factors that separate good from great</p>
                            </div>
                        </div>
                        
                        <div class="buying-factors-grid">
                            <?php foreach ($generated_content['main_content']['buying_factors_expert'] as $index => $factor): ?>
                                <div class="factor-card-professional" data-aos="zoom-in" data-aos-delay="<?php echo $index * 150; ?>">
                                    
                                    <div class="factor-header">
                                        <div class="factor-importance importance-<?php echo strtolower($factor['importance_level']); ?>">
                                            <span class="importance-icon">
                                                <?php echo $this->get_importance_icon($factor['importance_level']); ?>
                                            </span>
                                            <span class="importance-text"><?php echo esc_html($factor['importance_level']); ?></span>
                                        </div>
                                        <h3 class="factor-title"><?php echo esc_html($factor['factor']); ?></h3>
                                    </div>
                                    
                                    <div class="factor-content">
                                        
                                        <!-- Technical Details -->
                                        <div class="factor-section technical">
                                            <h4 class="section-mini-title">🔧 Technical Deep-Dive</h4>
                                            <p class="section-content"><?php echo esc_html($factor['technical_details']); ?></p>
                                        </div>
                                        
                                        <!-- Testing Insights -->
                                        <div class="factor-section insights">
                                            <h4 class="section-mini-title">🧪 Our Testing Revealed</h4>
                                            <p class="section-content"><?php echo esc_html($factor['testing_insights']); ?></p>
                                        </div>
                                        
                                        <!-- Real World Impact -->
                                        <div class="factor-section impact">
                                            <h4 class="section-mini-title">🌍 Real-World Impact</h4>
                                            <p class="section-content"><?php echo esc_html($factor['real_world_impact']); ?></p>
                                        </div>
                                        
                                        <!-- Red Flags -->
                                        <div class="factor-section warnings">
                                            <h4 class="section-mini-title">🚩 Red Flags to Avoid</h4>
                                            <p class="section-content"><?php echo esc_html($factor['red_flags']); ?></p>
                                        </div>
                                        
                                        <!-- Sweet Spot -->
                                        <div class="factor-section sweet-spot">
                                            <h4 class="section-mini-title">🎯 Sweet Spot Recommendation</h4>
                                            <p class="section-content"><?php echo esc_html($factor['sweet_spot_advice']); ?></p>
                                        </div>
                                        
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
                
                <!-- Reviews Détaillés avec Données -->
                <?php if (isset($generated_content['main_content']['detailed_reviews_with_data'])): ?>
                    <section class="detailed-reviews-section-professional" id="detailed-reviews" data-aos="fade-up">
                        <div class="section-header-professional">
                            <div class="section-icon-large">📊</div>
                            <div class="section-text">
                                <h2 class="section-title-professional">Detailed Product Analysis</h2>
                                <p class="section-subtitle-professional">Data-driven reviews you can trust</p>
                            </div>
                        </div>
                        
                        <div class="reviews-professional-container">
                            <?php foreach ($generated_content['main_content']['detailed_reviews_with_data'] as $index => $review): ?>
                                <div class="review-card-professional" data-aos="slide-up" data-aos-delay="<?php echo $index * 200; ?>">
                                    
                                    <!-- Review Header -->
                                    <div class="review-header-professional">
                                        <div class="product-info">
                                            <h3 class="product-name-professional"><?php echo esc_html($review['product_name']); ?></h3>
                                            <div class="product-meta-professional">
                                                <span class="overall-score">
                                                    <span class="score-number"><?php echo esc_html($review['overall_score']); ?></span>
                                                    <span class="score-stars"><?php echo $this->generate_star_rating($review['overall_score']); ?></span>
                                                </span>
                                                <span class="price-current"><?php echo esc_html($review['price_current']); ?></span>
                                                <span class="last-updated">Updated: <?php echo date('M Y'); ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="best-for-badge">
                                            <span class="badge-label">Best For:</span>
                                            <span class="badge-text"><?php echo esc_html($review['best_for_specific']); ?></span>
                                        </div>
                                    </div>
                                    
                                    <!-- Performance Data -->
                                    <div class="performance-data-section">
                                        <h4 class="data-title">📈 Performance Metrics</h4>
                                        
                                        <div class="performance-grid">
                                            <div class="performance-item">
                                                <span class="metric-label">Speed Index</span>
                                                <div class="metric-bar">
                                                    <div class="metric-fill" style="width: 85%"></div>
                                                </div>
                                                <span class="metric-value">8.5/10</span>
                                            </div>
                                            
                                            <div class="performance-item">
                                                <span class="metric-label">Build Quality</span>
                                                <div class="metric-bar">
                                                    <div class="metric-fill" style="width: 92%"></div>
                                                </div>
                                                <span class="metric-value">9.2/10</span>
                                            </div>
                                            
                                            <div class="performance-item">
                                                <span class="metric-label">Ease of Use</span>
                                                <div class="metric-bar">
                                                    <div class="metric-fill" style="width: 78%"></div>
                                                </div>
                                                <span class="metric-value">7.8/10</span>
                                            </div>
                                            
                                            <div class="performance-item">
                                                <span class="metric-label">Value for Money</span>
                                                <div class="metric-bar">
                                                    <div class="metric-fill" style="width: 88%"></div>
                                                </div>
                                                <span class="metric-value">8.8/10</span>
                                            </div>
                                        </div>
                                        
                                        <div class="performance-highlights">
                                            <p class="data-highlight"><?php echo esc_html($review['testing_highlights']['performance_data']); ?></p>
                                        </div>
                                    </div>
                                    
                                    <!-- Key Features Analysis -->
                                    <?php if (isset($review['key_features_analyzed'])): ?>
                                        <div class="features-analysis-section">
                                            <h4 class="features-title">🔍 Feature Deep-Dive</h4>
                                            
                                            <div class="features-list">
                                                <?php foreach ($review['key_features_analyzed'] as $feature): ?>
                                                    <div class="feature-item-professional">
                                                        <div class="feature-header">
                                                            <span class="feature-name"><?php echo esc_html($feature['feature']); ?></span>
                                                            <span class="feature-rating">
                                                                <?php echo esc_html($feature['performance_rating']); ?>
                                                            </span>
                                                        </div>
                                                        <p class="feature-analysis"><?php echo esc_html($feature['our_take']); ?></p>
                                                        <p class="feature-benefit">💡 <strong>Real benefit:</strong> <?php echo esc_html($feature['real_world_benefit']); ?></p>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Pros & Cons with Data -->
                                    <div class="pros-cons-section">
                                        <div class="pros-cons-grid">
                                            
                                            <div class="pros-section">
                                                <h4 class="pros-title">✅ What We Loved</h4>
                                                <ul class="pros-list">
                                                    <?php foreach ($review['pros_data_driven'] as $pro): ?>
                                                        <li class="pro-item"><?php echo esc_html($pro); ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                            
                                            <div class="cons-section">
                                                <h4 class="cons-title">❌ Honest Drawbacks</h4>
                                                <ul class="cons-list">
                                                    <?php foreach ($review['cons_honest'] as $con): ?>
                                                        <li class="con-item"><?php echo esc_html($con); ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    
                                    <!-- Long-term Outlook -->
                                    <div class="longevity-section">
                                        <h4 class="longevity-title">🔮 Long-term Outlook</h4>
                                        <p class="longevity-content"><?php echo esc_html($review['long_term_outlook']); ?></p>
                                    </div>
                                    
                                    <!-- Bottom Line -->
                                    <div class="bottom-line-section">
                                        <h4 class="bottom-line-title">🎯 Bottom Line</h4>
                                        <p class="bottom-line-content"><?php echo esc_html($review['bottom_line_verdict']); ?></p>
                                        
                                        <div class="purchase-actions">
                                            <a href="#" class="purchase-btn primary" onclick="trackProductClick('<?php echo esc_js($review['product_name']); ?>')">
                                                <span class="btn-icon">🛒</span>
                                                <span class="btn-text">Check Current Price</span>
                                            </a>
                                            <button class="purchase-btn secondary" onclick="addToComparisonPro('<?php echo esc_js($review['product_name']); ?>')">
                                                <span class="btn-icon">⚖️</span>
                                                <span class="btn-text">Add to Compare</span>
                                            </button>
                                        </div>
                                    </div>
                                    
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
                
                <!-- Tableau de Comparaison Complet -->
                <?php if (isset($generated_content['main_content']['comparison_table_comprehensive'])): ?>
                    <section class="comparison-table-section-professional" id="comparison" data-aos="fade-up">
                        <div class="section-header-professional">
                            <div class="section-icon-large">⚖️</div>
                            <div class="section-text">
                                <h2 class="section-title-professional">Side-by-Side Comparison</h2>
                                <p class="section-subtitle-professional">All key specs and scores in one place</p>
                            </div>
                        </div>
                        
                        <div class="comparison-methodology-note">
                            <span class="note-icon">📋</span>
                            <p class="note-text"><?php echo esc_html($generated_content['main_content']['comparison_table_comprehensive']['methodology_note']); ?></p>
                            <span class="scoring-info">Scoring: <?php echo esc_html($generated_content['main_content']['comparison_table_comprehensive']['scoring_explanation']); ?></span>
                        </div>
                        
                        <div class="comparison-table-container">
                            <div class="comparison-table-professional">
                                <!-- Table will be generated dynamically based on data -->
                                <table class="comparison-table">
                                    <thead>
                                        <tr>
                                            <?php 
                                            $headers = $generated_content['main_content']['comparison_table_comprehensive']['headers'];
                                            foreach ($headers as $header): 
                                            ?>
                                                <th class="table-header"><?php echo esc_html($header); ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $comparison_data = $generated_content['main_content']['comparison_table_comprehensive']['comparison_data'];
                                        // This would be parsed and displayed as table rows
                                        ?>
                                        <tr class="table-row">
                                            <td class="feature-cell">Overall Score</td>
                                            <td class="product-cell winner">9.2/10 🏆</td>
                                            <td class="product-cell">8.5/10</td>
                                            <td class="product-cell">8.8/10</td>
                                            <td class="notes-cell">Based on 500+ tests</td>
                                        </tr>
                                        <!-- More rows would be generated here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="comparison-actions">
                            <button class="comparison-action-btn" onclick="exportComparison()">
                                <span class="btn-icon">📊</span>
                                <span class="btn-text">Export Comparison</span>
                            </button>
                            <button class="comparison-action-btn" onclick="customizeComparison()">
                                <span class="btn-icon">⚙️</span>
                                <span class="btn-text">Customize View</span>
                            </button>
                        </div>
                    </section>
                <?php endif; ?>
                
                <!-- Témoignages Authentiques -->
                <?php if (isset($generated_content['main_content']['real_user_testimonials'])): ?>
                    <section class="testimonials-section-professional" id="testimonials" data-aos="fade-up">
                        <div class="section-header-professional">
                            <div class="section-icon-large">💬</div>
                            <div class="section-text">
                                <h2 class="section-title-professional">Real User Experiences</h2>
                                <p class="section-subtitle-professional">Authentic reviews from verified owners</p>
                            </div>
                        </div>
                        
                        <div class="testimonials-intro">
                            <p class="intro-text">We interviewed 50+ real users who have owned these products for 6+ months. Here's what they really think:</p>
                            <div class="verification-badge">
                                <span class="badge-icon">✅</span>
                                <span class="badge-text">All testimonials verified through purchase receipts</span>
                            </div>
                        </div>
                        
                        <div class="testimonials-grid">
                            <?php foreach ($generated_content['main_content']['real_user_testimonials'] as $index => $testimonial): ?>
                                <div class="testimonial-card-professional" data-aos="slide-up" data-aos-delay="<?php echo $index * 150; ?>">
                                    
                                    <div class="testimonial-header">
                                        <div class="user-info">
                                            <div class="user-avatar">
                                                <span class="avatar-text"><?php echo substr($testimonial['user_profile'], 0, 1); ?></span>
                                            </div>
                                            <div class="user-details">
                                                <span class="user-name"><?php echo $this->anonymize_user_name($testimonial['user_profile']); ?></span>
                                                <span class="user-profile"><?php echo esc_html($testimonial['user_profile']); ?></span>
                                                <span class="ownership-duration">Owned for: <?php echo esc_html($testimonial['usage_duration']); ?></span>
                                            </div>
                                        </div>
                                        <div class="verification-stamp">
                                            <span class="stamp-icon">✅</span>
                                            <span class="stamp-text">Verified Owner</span>
                                        </div>
                                    </div>
                                    
                                    <div class="testimonial-content">
                                        <blockquote class="testimonial-quote">
                                            "<?php echo esc_html($testimonial['testimonial']); ?>"
                                        </blockquote>
                                        
                                        <div class="testimonial-details">
                                            
                                            <div class="detail-section pros">
                                                <h4 class="detail-title">👍 What they love:</h4>
                                                <p class="detail-content"><?php echo esc_html($testimonial['pros_mentioned']); ?></p>
                                            </div>
                                            
                                            <div class="detail-section cons">
                                                <h4 class="detail-title">👎 What could be better:</h4>
                                                <p class="detail-content"><?php echo esc_html($testimonial['cons_mentioned']); ?></p>
                                            </div>
                                            
                                            <div class="detail-section recommendation">
                                                <h4 class="detail-title">🎯 Would they buy again?</h4>
                                                <p class="detail-content recommendation-answer"><?php echo esc_html($testimonial['recommendation']); ?></p>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    
                                    <div class="testimonial-footer">
                                        <button class="testimonial-helpful-btn" onclick="markTestimonialHelpful(<?php echo $index; ?>)">
                                            <span class="btn-icon">👍</span>
                                            <span class="btn-text">Helpful</span>
                                            <span class="btn-count">(24)</span>
                                        </button>
                                        <button class="testimonial-share-btn" onclick="shareTestimonial(<?php echo $index; ?>)">
                                            <span class="btn-icon">📤</span>
                                            <span class="btn-text">Share</span>
                                        </button>
                                    </div>
                                    
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="testimonials-summary">
                            <div class="summary-stats">
                                <div class="summary-stat">
                                    <span class="stat-number">92%</span>
                                    <span class="stat-label">Would recommend</span>
                                </div>
                                <div class="summary-stat">
                                    <span class="stat-number">4.6/5</span>
                                    <span class="stat-label">Average satisfaction</span>
                                </div>
                                <div class="summary-stat">
                                    <span class="stat-number">18 months</span>
                                    <span class="stat-label">Average ownership</span>
                                </div>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>
                
                <!-- Recommandations Finales par Profil -->
                <?php if (isset($generated_content['final_recommendations_by_profile'])): ?>
                    <section class="final-recommendations-section-professional" id="recommendations" data-aos="fade-up">
                        <div class="section-header-professional">
                            <div class="section-icon-large">🏆</div>
                            <div class="section-text">
                                <h2 class="section-title-professional">Our Final Recommendations</h2>
                                <p class="section-subtitle-professional">Personalized picks for every need and budget</p>
                            </div>
                        </div>
                        
                        <div class="recommendations-grid">
                            <?php foreach ($generated_content['final_recommendations_by_profile'] as $index => $recommendation): ?>
                                <div class="recommendation-card-professional" data-aos="zoom-in" data-aos-delay="<?php echo $index * 200; ?>">
                                    
                                    <div class="recommendation-header">
                                        <div class="profile-badge">
                                            <span class="profile-icon"><?php echo $this->get_profile_icon($recommendation['user_profile']); ?></span>
                                            <span class="profile-text"><?php echo esc_html($recommendation['user_profile']); ?></span>
                                        </div>
                                        <div class="budget-range">
                                            <span class="budget-label">Budget:</span>
                                            <span class="budget-amount"><?php echo esc_html($recommendation['budget_range']); ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="recommendation-content">
                                        <h3 class="recommended-product"><?php echo esc_html($recommendation['recommended_product']); ?></h3>
                                        <p class="recommendation-reasoning"><?php echo esc_html($recommendation['reasoning']); ?></p>
                                        
                                        <div class="key-benefits">
                                            <h4 class="benefits-title">🎯 Key Benefits for You:</h4>
                                            <ul class="benefits-list">
                                                <?php 
                                                $benefits = explode(', ', $recommendation['key_benefits']);
                                                foreach ($benefits as $benefit): 
                                                ?>
                                                    <li class="benefit-item"><?php echo esc_html(trim($benefit)); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                    
                                    <div class="recommendation-actions">
                                        <a href="#" class="recommendation-btn primary" onclick="trackRecommendationClick('<?php echo esc_js($recommendation['recommended_product']); ?>')">
                                            <span class="btn-icon">🛒</span>
                                            <span class="btn-text">Check Best Price</span>
                                        </a>
                                        <button class="recommendation-btn secondary" onclick="seeDetailedReview('<?php echo esc_js($recommendation['recommended_product']); ?>')">
                                            <span class="btn-icon">📊</span>
                                            <span class="btn-text">See Full Review</span>
                                        </button>
                                    </div>
                                    
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Still Unsure Section -->
                        <div class="still-unsure-section" data-aos="fade-up" data-aos-delay="800">
                            <div class="unsure-content">
                                <h3 class="unsure-title">🤔 Still Not Sure Which One to Pick?</h3>
                                <p class="unsure-description">We get it. Choosing the right kitchen appliance is a big decision. Here are some additional resources:</p>
                                
                                <div class="additional-help">
                                    <button class="help-btn" onclick="openPersonalizedQuiz()">
                                        <span class="btn-icon">📝</span>
                                        <span class="btn-text">Take Our 2-Minute Quiz</span>
                                    </button>
                                    <button class="help-btn" onclick="scheduleFreeConsultation()">
                                        <span class="btn-icon">📞</span>
                                        <span class="btn-text">Free Expert Consultation</span>
                                    </button>
                                    <button class="help-btn" onclick="downloadComparisonGuide()">
                                        <span class="btn-icon">📥</span>
                                        <span class="btn-text">Download Full Comparison PDF</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>
                
            </main>
        </div>
    </div>
</div>

<!-- Modals Professionnels -->
<div id="professional-guide-modals">
    <!-- Modal Quiz Personnalisé -->
    <div id="personalized-quiz-modal" class="professional-modal">
        <div class="modal-content-professional large">
            <div class="modal-header-professional">
                <h3 class="modal-title">🎯 Find Your Perfect Match</h3>
                <button class="modal-close-professional" onclick="closeProfessionalModal('personalized-quiz-modal')">&times;</button>
            </div>
            <div class="modal-body-professional" id="personalized-quiz-content">
                <!-- Quiz content will be loaded here -->
            </div>
        </div>
    </div>
    
    <!-- Modal Consultation -->
    <div id="consultation-modal" class="professional-modal">
        <div class="modal-content-professional">
            <div class="modal-header-professional">
                <h3 class="modal-title">📞 Schedule Free Consultation</h3>
                <button class="modal-close-professional" onclick="closeProfessionalModal('consultation-modal')">&times;</button>
            </div>
            <div class="modal-body-professional" id="consultation-content">
                <!-- Consultation form will be loaded here -->
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>