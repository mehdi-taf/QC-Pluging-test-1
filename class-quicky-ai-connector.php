<?php
// includes/class-quicky-ai-connector-enhanced.php

if (!defined('ABSPATH')) {
    exit;
}

// Inclure les prompts avancés
require_once QUICKY_AI_PATH . 'includes/quicky-ai-prompts-advanced.php';
require_once QUICKY_AI_PATH . 'includes/class-quicky-schema-manager.php';

class QuickyAIConnectorEnhanced {
    
    private $api_url = 'https://openrouter.ai/api/v1/chat/completions';
    private $schema_manager;
    
    public function __construct() {
        $this->schema_manager = new QuickySchemaManager();
        
        // Actions AJAX pour génération
        add_action('wp_ajax_generate_quicky_content_enhanced', array($this, 'handle_ajax_generation'));
        add_action('wp_ajax_nopriv_generate_quicky_content_enhanced', array($this, 'handle_ajax_generation'));
        
        // Scripts admin
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
        // Hook pour traiter les formulaires
        add_action('admin_init', array($this, 'handle_content_generation_form'));
        
        // Hook pour génération de schema automatique
        add_action('save_post', array($this, 'generate_schema_on_save'), 20);
    }
    
    private function get_api_key() {
        // TEMPORAIRE - Votre clé en dur pour test
        $hardcoded_key = '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';
        
        // Récupérer depuis la base de données
        $saved_key = get_option('quicky_ai_api_key', '');
        
        // Utiliser la clé sauvegardée si elle existe, sinon la clé en dur
        return !empty($saved_key) ? $saved_key : $hardcoded_key;
    }
    
    public function enqueue_admin_scripts() {
        wp_enqueue_script('jquery');
        wp_localize_script('jquery', 'QuickyAIEnhanced', array(
            'nonce' => wp_create_nonce('quicky_ai_enhanced_nonce'),
            'ajax_url' => admin_url('admin-ajax.php')
        ));
    }
    
    /**
     * GESTION FORMULAIRE AVEC PROMPTS ENRICHIS
     */
    public function handle_content_generation_form() {
        if (!isset($_POST['generate_quicky_content_enhanced']) || !wp_verify_nonce($_POST['quicky_nonce'], 'quicky_generate_content_enhanced')) {
            return;
        }
        
        if (!current_user_can('edit_posts')) {
            wp_die('Permissions insuffisantes');
        }
        
        $content_type = sanitize_text_field($_POST['content_type']);
        $content_data = $_POST;
        
        // Générer le contenu enrichi
        $generated_content = $this->generate_enhanced_content_by_type($content_type, $content_data);
        
        if ($generated_content) {
            $post_id = $this->create_enhanced_wordpress_post($generated_content, $content_type, $content_data);
            
            if ($post_id) {
                // Générer le schema markup automatiquement
                $this->generate_and_save_schema($post_id, $content_type, $generated_content);
                
                wp_redirect(admin_url('post.php?post=' . $post_id . '&action=edit&quicky_enhanced=1'));
                exit;
            }
        }
        
        wp_redirect(admin_url('admin.php?page=quicky-ai-create&error=generation_failed'));
        exit;
    }
    
    /**
     * GESTION AJAX ENRICHIE
     */
    public function handle_ajax_generation() {
        if (!wp_verify_nonce($_POST['nonce'], 'quicky_ai_enhanced_nonce')) {
            wp_send_json_error('Nonce verification failed');
        }
        
        $content_type = sanitize_text_field($_POST['content_type']);
        $content_data = $_POST['content_data'];
        
        $generated_content = $this->generate_enhanced_content_by_type($content_type, $content_data);
        
        if ($generated_content) {
            wp_send_json_success($generated_content);
        } else {
            wp_send_json_error('Enhanced content generation failed');
        }
    }
    
    /**
     * ROUTER PRINCIPAL POUR GÉNÉRATION ENRICHIE
     */
    private function generate_enhanced_content_by_type($content_type, $data) {
        $api_key = $this->get_api_key();
        
        if (empty($api_key)) {
            return false;
        }
        
        switch ($content_type) {
            case 'recipe':
                return $this->generate_enhanced_recipe_content($data);
            case 'buying-guide':
                return $this->generate_enhanced_buying_guide_content($data);
            case 'comparison':
                return $this->generate_enhanced_comparison_content($data);
            case 'blog-article':
                return $this->generate_enhanced_blog_content($data);
            default:
                return false;
        }
    }
    
    /**
     * GÉNÉRATION RECETTE ENRICHIE AVEC STORYTELLING
     */
    private function generate_enhanced_recipe_content($data) {
        $appliance = sanitize_text_field($data['appliance_type'] ?? '');
        $keyword = sanitize_text_field($data['recipe_keyword'] ?? '');
        $custom_title = sanitize_text_field($data['custom_title'] ?? '');
        $cooking_time = intval($data['cooking_time'] ?? 30);
        $difficulty = sanitize_text_field($data['difficulty'] ?? 'easy');
        $serves = intval($data['serves'] ?? 4);
        $cuisine_type = sanitize_text_field($data['cuisine_type'] ?? '');
        $dietary_tags = sanitize_text_field($data['dietary_tags'] ?? '');
        
        // Utiliser le prompt storytelling avancé
        $prompt = QuickyAIPromptsAdvanced::get_recipe_prompt_storytelling(
            $appliance, $keyword, $custom_title, $cooking_time, 
            $difficulty, $serves, $cuisine_type, $dietary_tags
        );
        
        $ai_response = $this->call_claude_api_enhanced($prompt, 'recipe');
        
        if ($ai_response) {
            // Enrichir avec des métadonnées SEO automatiques ultra-avancées
            $ai_response['enhanced_seo_data'] = $this->generate_enhanced_recipe_seo_data($ai_response, $appliance, $keyword);
            $ai_response['schema_markup'] = $this->schema_manager->generate_content_schema('recipe', null);
            $ai_response['auto_tags'] = $this->get_enhanced_recipe_tags($appliance, $keyword, $cuisine_type, $dietary_tags, $ai_response);
            $ai_response['content_type'] = 'recipe';
            $ai_response['appliance_type'] = $appliance;
            $ai_response['content_quality_score'] = $this->calculate_content_quality_score($ai_response, 'recipe');
            $ai_response['engagement_predictions'] = $this->predict_engagement_metrics($ai_response, 'recipe');
        }
        
        return $ai_response;
    }
    
    /**
     * GÉNÉRATION GUIDE D'ACHAT PROFESSIONNEL
     */
    private function generate_enhanced_buying_guide_content($data) {
        $category = sanitize_text_field($data['product_category'] ?? '');
        $budget_range = sanitize_text_field($data['budget_range'] ?? '');
        $target_audience = sanitize_text_field($data['target_audience'] ?? '');
        $key_features = sanitize_text_field($data['key_features'] ?? '');
        
        // Utiliser le prompt professionnel avancé
        $prompt = QuickyAIPromptsAdvanced::get_buying_guide_prompt_professional(
            $category, $budget_range, $target_audience, $key_features
        );
        
        $ai_response = $this->call_claude_api_enhanced($prompt, 'buying-guide');
        
        if ($ai_response) {
            $ai_response['enhanced_seo_data'] = $this->generate_enhanced_guide_seo_data($ai_response, $category);
            $ai_response['schema_markup'] = $this->schema_manager->generate_content_schema('buying-guide', null);
            $ai_response['auto_tags'] = $this->get_enhanced_guide_tags($category, $budget_range, $target_audience, $ai_response);
            $ai_response['content_type'] = 'buying-guide';
            $ai_response['content_quality_score'] = $this->calculate_content_quality_score($ai_response, 'buying-guide');
            $ai_response['credibility_score'] = $this->calculate_credibility_score($ai_response);
            $ai_response['commercial_value'] = $this->calculate_commercial_value($ai_response);
        }
        
        return $ai_response;
    }
    
    /**
     * GÉNÉRATION COMPARATIF ULTRA-DÉTAILLÉ
     */
    private function generate_enhanced_comparison_content($data) {
        $product1 = sanitize_text_field($data['product_1'] ?? '');
        $product2 = sanitize_text_field($data['product_2'] ?? '');
        $comparison_focus = sanitize_text_field($data['comparison_focus'] ?? '');
        
        // Utiliser le prompt comparatif détaillé
        $prompt = QuickyAIPromptsAdvanced::get_comparison_prompt_detailed(
            $product1, $product2, $comparison_focus
        );
        
        $ai_response = $this->call_claude_api_enhanced($prompt, 'comparison');
        
        if ($ai_response) {
            $ai_response['enhanced_seo_data'] = $this->generate_enhanced_comparison_seo_data($ai_response, $product1, $product2);
            $ai_response['schema_markup'] = $this->schema_manager->generate_content_schema('comparison', null);
            $ai_response['auto_tags'] = $this->get_enhanced_comparison_tags($product1, $product2, $ai_response);
            $ai_response['content_type'] = 'comparison';
            $ai_response['objectivity_score'] = $this->calculate_objectivity_score($ai_response);
            $ai_response['decision_value'] = $this->calculate_decision_value($ai_response);
        }
        
        return $ai_response;
    }
    
    /**
     * APPEL API CLAUDE OPTIMISÉ AVEC RETRY ET VALIDATION
     */
    private function call_claude_api_enhanced($prompt, $content_type) {
        $api_key = $this->get_api_key();
        $model = get_option('quicky_ai_model', 'anthropic/claude-3.5-sonnet');
        
        // Système de prompts contextuels selon le type
        $system_prompts = array(
            'recipe' => 'You are a world-class culinary expert and food scientist specializing in kitchen appliances. You create engaging, scientifically-accurate recipes that combine storytelling with practical expertise. You understand food science, nutrition, and how different cooking methods affect flavor and texture. Always provide detailed, actionable instructions that help home cooks succeed.',
            
            'buying-guide' => 'You are a professional product testing expert with years of experience evaluating kitchen appliances and cooking equipment. You have access to testing labs, industry connections, and deep technical knowledge. You create buying guides that establish credibility through detailed testing methodology and honest, data-driven recommendations.',
            
            'comparison' => 'You are an objective product analyst who specializes in head-to-head comparisons. You use rigorous testing methodologies, measurable criteria, and transparent scoring systems. Your goal is to help consumers make confident purchase decisions based on evidence and clear reasoning.',
            
            'blog-article' => 'You are an expert content creator specializing in kitchen tips, cooking techniques, and culinary education. You make complex cooking concepts accessible and engaging for home cooks of all skill levels.'
        );
        
        $system_prompt = $system_prompts[$content_type] ?? $system_prompts['recipe'];
        
        $body = array(
            'model' => $model,
            'messages' => array(
                array(
                    'role' => 'system',
                    'content' => $system_prompt
                ),
                array(
                    'role' => 'user', 
                    'content' => $prompt
                )
            ),
            'max_tokens' => 12000, // Augmenté pour le contenu enrichi
            'temperature' => 0.7,
            'top_p' => 0.9,
            'frequency_penalty' => 0.1, // Réduire la répétition
            'presence_penalty' => 0.1   // Encourager la diversité
        );
        
        // Système de retry avec backoff
        $max_retries = 3;
        $retry_delay = 1; // secondes
        
        for ($attempt = 1; $attempt <= $max_retries; $attempt++) {
            $response = wp_remote_post($this->api_url, array(
                'timeout' => 180, // 3 minutes pour le contenu enrichi
                'headers' => array(
                    'Authorization' => 'Bearer ' . $api_key,
                    'Content-Type' => 'application/json',
                    'HTTP-Referer' => home_url(),
                    'X-Title' => 'Quicky Cooking AI Enhanced Content Generator'
                ),
                'body' => json_encode($body)
            ));
            
            if (is_wp_error($response)) {
                error_log('QUICKY AI ENHANCED ERROR (Attempt ' . $attempt . '): ' . $response->get_error_message());
                
                if ($attempt < $max_retries) {
                    sleep($retry_delay);
                    $retry_delay *= 2; // Exponential backoff
                    continue;
                }
                return false;
            }
            
            $response_code = wp_remote_retrieve_response_code($response);
            if ($response_code === 200) {
                break;
            } elseif ($response_code === 429) {
                // Rate limit - attendre plus longtemps
                if ($attempt < $max_retries) {
                    sleep($retry_delay * 2);
                    $retry_delay *= 2;
                    continue;
                }
                return false;
            } else {
                error_log('QUICKY AI ENHANCED HTTP ERROR: ' . $response_code);
                if ($attempt < $max_retries) {
                    sleep($retry_delay);
                    continue;
                }
                return false;
            }
        }
        
        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body, true);
        
        if (isset($data['choices'][0]['message']['content'])) {
            $content = $data['choices'][0]['message']['content'];
            
            // Nettoyer et extraire le JSON
            $content = trim($content);
            
            // Supprimer les markdown code blocks si présents
            $content = preg_replace('/```json\s*/', '', $content);
            $content = preg_replace('/```\s*$/', '', $content);
            
            // Extraire le JSON même s'il y a du texte avant/après
            if (preg_match('/\{.*\}/s', $content, $matches)) {
                $content = $matches[0];
            }
            
            $parsed_content = json_decode($content, true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                // Valider la qualité du contenu généré
                if ($this->validate_enhanced_content($parsed_content, $content_type)) {
                    return $parsed_content;
                } else {
                    error_log('QUICKY AI ENHANCED: Content validation failed for ' . $content_type);
                    return false;
                }
            } else {
                error_log('QUICKY AI ENHANCED JSON ERROR: ' . json_last_error_msg());
                error_log('CONTENT: ' . $content);
                return false;
            }
        }
        
        return false;
    }
    
    /**
     * VALIDATION DU CONTENU ENRICHI
     */
    private function validate_enhanced_content($content, $content_type) {
        $required_fields = array(
            'recipe' => [
                'title', 'meta_description', 'storytelling_intro', 'why_this_recipe_works',
                'main_content', 'seo_optimized_sections'
            ],
            'buying-guide' => [
                'title', 'meta_description', 'credibility_establishment', 'executive_summary',
                'main_content', 'seo_optimized_sections'
            ],
            'comparison' => [
                'title', 'meta_description', 'comparison_overview', 'executive_summary',
                'detailed_comparison_categories', 'final_verdict'
            ]
        );
        
        if (!isset($required_fields[$content_type])) {
            return false;
        }
        
        foreach ($required_fields[$content_type] as $field) {
            if (!isset($content[$field]) || empty($content[$field])) {
                error_log("QUICKY AI VALIDATION: Missing required field '{$field}' for {$content_type}");
                return false;
            }
        }
        
        // Validation de longueur minimale
        $min_lengths = array(
            'recipe' => 500,      // mots minimum
            'buying-guide' => 800,
            'comparison' => 600
        );
        
        $word_count = $this->estimate_word_count($content);
        if ($word_count < $min_lengths[$content_type]) {
            error_log("QUICKY AI VALIDATION: Content too short ({$word_count} words) for {$content_type}");
            return false;
        }
        
        return true;
    }
    
    /**
     * GÉNÉRATION SEO ENRICHIE
     */
    private function generate_enhanced_recipe_seo_data($content, $appliance, $keyword) {
        $base_seo = array(
            'focus_keyphrase' => $keyword . ' ' . str_replace('-', ' ', $appliance),
            'meta_title' => $this->optimize_title_length($content['title']),
            'canonical_url' => '',
            'og_title' => $content['title'],
            'og_description' => $content['meta_description'],
            'twitter_title' => $content['title'],
            'twitter_description' => $content['meta_description']
        );
        
        // SEO enrichi avec topic clusters
        $base_seo['topic_clusters'] = $this->generate_topic_clusters($keyword, $appliance, 'recipe');
        $base_seo['long_tail_keywords'] = $this->extract_long_tail_keywords($content);
        $base_seo['featured_snippet_optimized'] = $this->optimize_for_featured_snippets($content, 'recipe');
        $base_seo['internal_linking_suggestions'] = $this->generate_internal_linking_suggestions($keyword, $appliance);
        $base_seo['content_gap_analysis'] = $this->analyze_content_gaps($content, 'recipe');
        
        return $base_seo;
    }
    
    private function generate_enhanced_guide_seo_data($content, $category) {
        $base_seo = array(
            'focus_keyphrase' => 'best ' . $category,
            'meta_title' => $this->optimize_title_length($content['title']),
            'og_title' => $content['title'],
            'og_description' => $content['meta_description']
        );
        
        // SEO enrichi pour guides
        $base_seo['buyer_intent_keywords'] = $this->extract_buyer_intent_keywords($content, $category);
        $base_seo['commercial_keywords'] = $this->extract_commercial_keywords($content);
        $base_seo['comparison_opportunities'] = $this->identify_comparison_opportunities($content);
        $base_seo['product_schema_candidates'] = $this->extract_product_schema_candidates($content);
        
        return $base_seo;
    }
    
    private function generate_enhanced_comparison_seo_data($content, $product1, $product2) {
        $base_seo = array(
            'focus_keyphrase' => $product1 . ' vs ' . $product2,
            'meta_title' => $this->optimize_title_length($content['title']),
            'og_title' => $content['title'],
            'og_description' => $content['meta_description']
        );
        
        // SEO enrichi pour comparatifs
        $base_seo['vs_keywords'] = $this->generate_vs_keywords($product1, $product2);
        $base_seo['decision_keywords'] = $this->extract_decision_keywords($content);
        $base_seo['brand_comparison_opportunities'] = $this->identify_brand_comparisons($content);
        
        return $base_seo;
    }
    
    /**
     * GÉNÉRATION DE TAGS ENRICHIS
     */
    private function get_enhanced_recipe_tags($appliance, $keyword, $cuisine_type, $dietary_tags, $content) {
        $tags = [];
        
        // Tags de base
        $tags[] = str_replace('-', ' ', $appliance);
        $tags[] = $keyword;
        $tags[] = $appliance . ' recipe';
        
        // Tags du contenu généré
        if (isset($content['recipe_tags_semantic'])) {
            $tags = array_merge($tags, $content['recipe_tags_semantic']);
        }
        
        // Tags de difficulté et temps
        if (isset($content['difficulty'])) {
            $tags[] = $content['difficulty'] . ' recipe';
        }
        
        if (isset($content['total_time'])) {
            $time = intval($content['total_time']);
            if ($time <= 30) {
                $tags[] = 'quick recipe';
                $tags[] = '30 minute meal';
            } elseif ($time <= 60) {
                $tags[] = 'under 1 hour';
            }
        }
        
        // Tags nutritionnels
        if (isset($content['nutrition_per_serving'])) {
            $nutrition = $content['nutrition_per_serving'];
            if (isset($nutrition['calories']) && intval($nutrition['calories']) < 300) {
                $tags[] = 'low calorie';
            }
            if (isset($nutrition['protein']) && intval($nutrition['protein']) > 20) {
                $tags[] = 'high protein';
            }
        }
        
        // Tags de cuisine
        if ($cuisine_type) {
            $tags[] = $cuisine_type . ' cuisine';
            $tags[] = $cuisine_type . ' recipe';
        }
        
        // Tags diététiques
        if ($dietary_tags) {
            $diet_tags = explode(',', $dietary_tags);
            $tags = array_merge($tags, array_map('trim', $diet_tags));
        }
        
        // Tags d'émotion/occasion du storytelling
        if (isset($content['storytelling_intro'])) {
            $tags[] = 'comfort food';
            $tags[] = 'family recipe';
            $tags[] = 'weeknight dinner';
        }
        
        // Tags SEO long-tail
        if (isset($content['seo_optimized_sections']['long_tail_keywords_natural'])) {
            $tags = array_merge($tags, $content['seo_optimized_sections']['long_tail_keywords_natural']);
        }
        
        return array_unique(array_filter($tags));
    }
    
    /**
     * CALCUL DE SCORES DE QUALITÉ
     */
    private function calculate_content_quality_score($content, $content_type) {
        $score = 0;
        $max_score = 100;
        
        // Critères généraux (40 points)
        if (isset($content['title']) && strlen($content['title']) > 30) $score += 5;
        if (isset($content['meta_description']) && strlen($content['meta_description']) >= 150) $score += 5;
        if (isset($content['seo_optimized_sections']['faq_for_featured_snippets'])) $score += 10;
        
        $word_count = $this->estimate_word_count($content);
        if ($word_count > 1000) $score += 10;
        if ($word_count > 2000) $score += 10;
        
        // Critères spécifiques par type (60 points)
        switch ($content_type) {
            case 'recipe':
                if (isset($content['storytelling_intro'])) $score += 15;
                if (isset($content['why_this_recipe_works'])) $score += 15;
                if (isset($content['main_content']['troubleshooting_comprehensive'])) $score += 10;
                if (isset($content['main_content']['variations_and_adaptations'])) $score += 10;
                if (isset($content['nutrition_per_serving'])) $score += 10;
                break;
                
            case 'buying-guide':
                if (isset($content['credibility_establishment'])) $score += 15;
                if (isset($content['testing_methodology_detailed'])) $score += 15;
                if (isset($content['main_content']['real_user_testimonials'])) $score += 15;
                if (isset($content['main_content']['detailed_reviews_with_data'])) $score += 15;
                break;
                
            case 'comparison':
                if (isset($content['executive_summary'])) $score += 15;
                if (isset($content['detailed_comparison_categories'])) $score += 15;
                if (isset($content['final_verdict'])) $score += 15;
                if (isset($content['decision_framework'])) $score += 15;
                break;
        }
        
        return min($score, $max_score);
    }
    
    /**
     * PRÉDICTION D'ENGAGEMENT
     */
    private function predict_engagement_metrics($content, $content_type) {
        $predictions = array(
            'estimated_time_on_page' => 0,
            'bounce_rate_prediction' => 0,
            'social_sharing_potential' => 0,
            'return_visitor_likelihood' => 0
        );
        
        // Calculs basés sur le contenu
        $word_count = $this->estimate_word_count($content);
        $predictions['estimated_time_on_page'] = round($word_count / 200 * 60); // secondes
        
        // Facteurs d'engagement
        $engagement_factors = 0;
        if (isset($content['storytelling_intro'])) $engagement_factors++;
        if (isset($content['seo_optimized_sections']['faq_for_featured_snippets'])) $engagement_factors++;
        if ($word_count > 1500) $engagement_factors++;
        
        $predictions['bounce_rate_prediction'] = max(20, 60 - ($engagement_factors * 10));
        $predictions['social_sharing_potential'] = min(90, $engagement_factors * 20 + 30);
        $predictions['return_visitor_likelihood'] = min(80, $engagement_factors * 15 + 25);
        
        return $predictions;
    }
    
    /**
     * CRÉATION DE POST WORDPRESS ENRICHI
     */
    private function create_enhanced_wordpress_post($content, $content_type, $original_data) {
        // Construire le contenu WordPress ultra-riche
        $wordpress_content = $this->build_enhanced_wordpress_content($content, $content_type);
        
        // Déterminer la catégorie optimisée
        $category_id = $this->get_optimized_content_category($content_type, $original_data, $content);
        
        // Tags enrichis
        $enhanced_tags = $content['auto_tags'] ?? [];
        
        $post_data = array(
            'post_title' => $content['title'],
            'post_content' => $wordpress_content,
            'post_excerpt' => $content['excerpt'] ?? $content['meta_description'],
            'post_status' => 'draft',
            'post_type' => 'post',
            'post_category' => array($category_id),
            'meta_input' => array(
                '_quicky_content_type' => $content_type,
                '_quicky_generated_content_enhanced' => json_encode($content),
                '_quicky_generation_date' => current_time('mysql'),
                '_quicky_content_quality_score' => $content['content_quality_score'] ?? 0,
                '_quicky_enhanced_version' => '2.0',
                
                // SEO Meta enrichi
                '_yoast_wpseo_title' => $content['enhanced_seo_data']['meta_title'] ?? $content['title'],
                '_yoast_wpseo_metadesc' => $content['meta_description'],
                '_yoast_wpseo_focuskw' => $content['enhanced_seo_data']['focus_keyphrase'] ?? '',
                '_yoast_wpseo_linkdex' => min(100, ($content['content_quality_score'] ?? 70)),
                
                // Schema markup enrichi
                '_quicky_schema_markup_enhanced' => json_encode($content['schema_markup'] ?? []),
                '_quicky_topic_clusters' => json_encode($content['enhanced_seo_data']['topic_clusters'] ?? []),
                '_quicky_internal_linking_suggestions' => json_encode($content['enhanced_seo_data']['internal_linking_suggestions'] ?? [])
            )
        );
        
        $post_id = wp_insert_post($post_data);
        
        if (!is_wp_error($post_id)) {
            // Ajouter les tags enrichis
            if (!empty($enhanced_tags)) {
                wp_set_post_tags($post_id, $enhanced_tags);
            }
            
            // Sauvegarder les métadonnées spécifiques enrichies
            $this->save_enhanced_content_metadata($post_id, $content, $content_type, $original_data);
            
            // Log de génération réussie
            error_log("QUICKY AI ENHANCED: Successfully generated {$content_type} (Post ID: {$post_id}) with quality score: " . ($content['content_quality_score'] ?? 'N/A'));
            
            return $post_id;
        }
        
        return false;
    }
    
    /**
     * GÉNÉRATION ET SAUVEGARDE DE SCHEMA
     */
    private function generate_and_save_schema($post_id, $content_type, $generated_content) {
        // Simuler les données pour la génération de schema
        update_post_meta($post_id, '_quicky_generated_content', json_encode($generated_content));
        
        $schema = $this->schema_manager->generate_content_schema($content_type, $post_id);
        
        if ($schema) {
            update_post_meta($post_id, '_quicky_schema_markup_enhanced', json_encode($schema));
            $this->schema_manager->log_schema_generation($content_type, $post_id, $schema);
        }
    }
    
    /**
     * HOOK POUR GÉNÉRATION AUTO DE SCHEMA
     */
    public function generate_schema_on_save($post_id) {
        $content_type = get_post_meta($post_id, '_quicky_content_type', true);
        
        if ($content_type && !get_post_meta($post_id, '_quicky_schema_markup_enhanced', true)) {
            $this->generate_and_save_schema($post_id, $content_type, []);
        }
    }
    
    /**
     * FONCTIONS UTILITAIRES
     */
    
    private function estimate_word_count($content) {
        $text = '';
        
        // Extraire tout le texte du contenu JSON
        array_walk_recursive($content, function($value) use (&$text) {
            if (is_string($value)) {
                $text .= ' ' . $value;
            }
        });
        
        return str_word_count(strip_tags($text));
    }
    
    private function optimize_title_length($title) {
        if (strlen($title) <= 60) {
            return $title;
        }
        
        return substr($title, 0, 57) . '...';
    }
    
    private function generate_topic_clusters($keyword, $appliance, $content_type) {
        $clusters = array(
            'primary' => $keyword,
            'secondary' => array(
                str_replace('-', ' ', $appliance) . ' recipes',
                'how to use ' . str_replace('-', ' ', $appliance),
                'best ' . str_replace('-', ' ', $appliance)
            ),
            'long_tail' => array(
                'easy ' . $keyword . ' recipe',
                $keyword . ' for beginners',
                'healthy ' . $keyword . ' recipe'
            )
        );
        
        return $clusters;
    }
    
    private function extract_long_tail_keywords($content) {
        $keywords = [];
        
        if (isset($content['seo_optimized_sections']['long_tail_keywords_natural'])) {
            $keywords = $content['seo_optimized_sections']['long_tail_keywords_natural'];
        }
        
        return $keywords;
    }
    
    private function build_enhanced_wordpress_content($content, $content_type) {
        switch ($content_type) {
            case 'recipe':
                return $this->build_enhanced_recipe_content($content);
            case 'buying-guide':
                return $this->build_enhanced_guide_content($content);
            case 'comparison':
                return $this->build_enhanced_comparison_content($content);
            default:
                return $this->build_basic_content($content);
        }
    }
    
    private function build_enhanced_recipe_content($content) {
        $html = '';
        
        // Introduction émotionnelle
        if (isset($content['storytelling_intro'])) {
            $intro = $content['storytelling_intro'];
            $html .= '<div class="recipe-storytelling-intro">';
            $html .= '<p class="emotional-hook">' . esc_html($intro['emotional_hook']) . '</p>';
            $html .= '<p class="transformation-promise">' . esc_html($intro['transformation_promise']) . '</p>';
            if (isset($intro['credibility_statement'])) {
                $html .= '<p class="credibility-statement">' . esc_html($intro['credibility_statement']) . '</p>';
            }
            $html .= '</div>';
        }
        
        // Section "Pourquoi ça marche"
        if (isset($content['why_this_recipe_works'])) {
            $science = $content['why_this_recipe_works'];
            $html .= '<h2>🧪 Why This Recipe Works</h2>';
            $html .= '<div class="recipe-science-section">';
            $html .= '<p class="scientific-explanation">' . esc_html($science['scientific_explanation']) . '</p>';
            if (isset($science['technique_benefits'])) {
                $html .= '<p class="technique-benefits">' . esc_html($science['technique_benefits']) . '</p>';
            }
            $html .= '</div>';
        }
        
        // Ingrédients avec science
        if (isset($content['main_content']['ingredients_with_science'])) {
            $html .= '<h2>🥕 Ingredients</h2>';
            $html .= '<ul class="recipe-ingredients-enhanced">';
            foreach ($content['main_content']['ingredients_with_science'] as $ingredient) {
                $html .= '<li class="ingredient-enhanced">';
                $html .= '<span class="ingredient-name">' . esc_html($ingredient['ingredient']) . '</span>';
                if (isset($ingredient['purpose'])) {
                    $html .= '<span class="ingredient-purpose">💡 ' . esc_html($ingredient['purpose']) . '</span>';
                }
                $html .= '</li>';
            }
            $html .= '</ul>';
        }
        
        // Instructions masterclass
        if (isset($content['main_content']['step_by_step_masterclass'])) {
            $html .= '<h2>📝 Instructions</h2>';
            $html .= '<ol class="recipe-instructions-masterclass">';
            foreach ($content['main_content']['step_by_step_masterclass'] as $step) {
                $html .= '<li class="instruction-step-enhanced">';
                $html .= '<div class="step-action">' . esc_html($step['action']) . '</div>';
                if (isset($step['why_this_step'])) {
                    $html .= '<div class="step-why">🤔 Why: ' . esc_html($step['why_this_step']) . '</div>';
                }
                if (isset($step['sensory_cues'])) {
                    $html .= '<div class="step-cues">👀 Look for: ' . esc_html($step['sensory_cues']) . '</div>';
                }
                if (isset($step['pro_tip'])) {
                    $html .= '<div class="step-pro-tip">💡 Pro Tip: ' . esc_html($step['pro_tip']) . '</div>';
                }
                $html .= '</li>';
            }
            $html .= '</ol>';
        }
        
        // Troubleshooting complet
        if (isset($content['main_content']['troubleshooting_comprehensive'])) {
            $html .= '<h2>🔧 Troubleshooting Guide</h2>';
            $html .= '<div class="troubleshooting-comprehensive">';
            foreach ($content['main_content']['troubleshooting_comprehensive'] as $trouble) {
                $html .= '<div class="troubleshooting-item">';
                $html .= '<h4 class="problem">❌ ' . esc_html($trouble['problem']) . '</h4>';
                $html .= '<p class="cause"><strong>Cause:</strong> ' . esc_html($trouble['cause']) . '</p>';
                $html .= '<p class="solution"><strong>Solution:</strong> ' . esc_html($trouble['solution']) . '</p>';
                $html .= '<p class="prevention"><strong>Prevention:</strong> ' . esc_html($trouble['prevention']) . '</p>';
                $html .= '</div>';
            }
            $html .= '</div>';
        }
        
        // FAQ optimisée
        if (isset($content['seo_optimized_sections']['faq_for_featured_snippets'])) {
            $html .= '<h2>❓ Frequently Asked Questions</h2>';
            $html .= '<div class="faq-featured-snippets">';
            foreach ($content['seo_optimized_sections']['faq_for_featured_snippets'] as $faq) {
                $html .= '<div class="faq-item-enhanced">';
                $html .= '<h3 class="faq-question">' . esc_html($faq['question']) . '</h3>';
                $html .= '<div class="faq-answer">' . esc_html($faq['answer']) . '</div>';
                $html .= '</div>';
            }
            $html .= '</div>';
        }
        
        return $html;
    }
    
    // Placeholder pour autres méthodes
    private function build_enhanced_guide_content($content) {
        // Implementation similaire pour guides
        return '<p>Enhanced guide content will be built here</p>';
    }
    
    private function build_enhanced_comparison_content($content) {
        // Implementation similaire pour comparatifs
        return '<p>Enhanced comparison content will be built here</p>';
    }
    
    private function build_basic_content($content) {
        return '<p>' . esc_html($content['main_content']['introduction'] ?? 'Generated content') . '</p>';
    }
    
    private function save_enhanced_content_metadata($post_id, $content, $content_type, $original_data) {
        // Sauvegarder toutes les métadonnées enrichies selon le type
        if ($content_type === 'recipe') {
            if (isset($content['prep_time'])) update_post_meta($post_id, '_quicky_prep_time', $content['prep_time']);
            if (isset($content['cook_time'])) update_post_meta($post_id, '_quicky_cook_time', $content['cook_time']);
            if (isset($content['total_time'])) update_post_meta($post_id, '_quicky_total_time', $content['total_time']);
            if (isset($content['servings'])) update_post_meta($post_id, '_quicky_servings', $content['servings']);
            if (isset($content['difficulty'])) update_post_meta($post_id, '_quicky_difficulty', $content['difficulty']);
            
            if (isset($original_data['appliance_type'])) {
                update_post_meta($post_id, '_quicky_appliance_type', $original_data['appliance_type']);
            }
            
            if (isset($content['nutrition_per_serving'])) {
                update_post_meta($post_id, '_quicky_nutrition', json_encode($content['nutrition_per_serving']));
            }
        }
        
        // Sauvegarder les prédictions d'engagement
        if (isset($content['engagement_predictions'])) {
            update_post_meta($post_id, '_quicky_engagement_predictions', json_encode($content['engagement_predictions']));
        }
        
        // Sauvegarder les scores de qualité
        if (isset($content['content_quality_score'])) {
            update_post_meta($post_id, '_quicky_content_quality_score', $content['content_quality_score']);
        }
    }
    
    // Méthodes placeholder pour calculs avancés
    private function calculate_credibility_score($content) {
        return rand(75, 95); // Placeholder
    }
    
    private function calculate_commercial_value($content) {
        return rand(60, 90); // Placeholder
    }
    
    private function calculate_objectivity_score($content) {
        return rand(80, 95); // Placeholder
    }
    
    private function calculate_decision_value($content) {
        return rand(70, 90); // Placeholder
    }
    
    private function get_optimized_content_category($content_type, $original_data, $content) {
        // Logique similaire à la version originale mais optimisée
        return 1; // Placeholder
    }
    
    // Méthodes SEO placeholder
    private function optimize_for_featured_snippets($content, $type) { return []; }
    private function generate_internal_linking_suggestions($keyword, $appliance) { return []; }
    private function analyze_content_gaps($content, $type) { return []; }
    private function extract_buyer_intent_keywords($content, $category) { return []; }
    private function extract_commercial_keywords($content) { return []; }
    private function identify_comparison_opportunities($content) { return []; }
    private function extract_product_schema_candidates($content) { return []; }
    private function generate_vs_keywords($product1, $product2) { return []; }
    private function extract_decision_keywords($content) { return []; }
    private function identify_brand_comparisons($content) { return []; }
    private function get_enhanced_guide_tags($category, $budget_range, $target_audience, $content) { return []; }
    private function get_enhanced_comparison_tags($product1, $product2, $content) { return []; }
}

// Initialisation
new QuickyAIConnectorEnhanced();