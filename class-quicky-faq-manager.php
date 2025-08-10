<?php
// includes/class-quicky-faq-manager.php

if (!defined('ABSPATH')) {
    exit;
}

class QuickyFAQManager {
    
    private $featured_snippet_patterns = [];
    private $question_templates = [];
    private $faq_analytics = [];
    
    public function __construct() {
        add_action('init', array($this, 'init_faq_manager'));
        add_action('wp_ajax_generate_auto_faq', array($this, 'ajax_generate_auto_faq'));
        add_action('wp_ajax_optimize_faq_for_snippets', array($this, 'ajax_optimize_faq_for_snippets'));
        add_action('wp_ajax_analyze_faq_performance', array($this, 'ajax_analyze_faq_performance'));
        add_action('wp_ajax_get_faq_suggestions', array($this, 'ajax_get_faq_suggestions'));
        
        // Hook pour génération automatique de FAQ lors de la création de contenu
        add_action('quicky_content_generated', array($this, 'auto_generate_faq_on_content_creation'), 10, 2);
        
        // Hook pour mise à jour du schema FAQPage
        add_action('save_post', array($this, 'update_faq_schema_on_save'), 20);
        
        $this->init_question_templates();
        $this->init_featured_snippet_patterns();
    }
    
    /**
     * INITIALISATION DU GESTIONNAIRE FAQ
     */
    public function init_faq_manager() {
        // Enregistrer les types de questions optimisées pour les featured snippets
        $this->register_question_types();
        
        // Initialiser les patterns de reconnaissance
        $this->init_content_analysis_patterns();
        
        // Charger les données d'analytics FAQ
        $this->load_faq_analytics_data();
    }
    
    /**
     * GÉNÉRATION AUTOMATIQUE DE FAQ
     */
    public function generate_automatic_faq($post_id, $content_type = null, $content_data = null) {
        if (!$content_data) {
            $content_data = json_decode(get_post_meta($post_id, '_quicky_generated_content_enhanced', true), true);
        }
        
        if (!$content_type) {
            $content_type = get_post_meta($post_id, '_quicky_content_type', true);
        }
        
        $post_content = get_post_field('post_content', $post_id);
        $post_title = get_the_title($post_id);
        
        $generated_faq = array();
        
        // Générer FAQ basée sur le type de contenu
        switch ($content_type) {
            case 'recipe':
                $generated_faq = $this->generate_recipe_faq($post_title, $content_data, $post_content);
                break;
            case 'buying-guide':
                $generated_faq = $this->generate_buying_guide_faq($post_title, $content_data, $post_content);
                break;
            case 'comparison':
                $generated_faq = $this->generate_comparison_faq($post_title, $content_data, $post_content);
                break;
            case 'blog-article':
                $generated_faq = $this->generate_article_faq($post_title, $content_data, $post_content);
                break;
            default:
                $generated_faq = $this->generate_generic_faq($post_title, $post_content);
        }
        
        // Optimiser chaque FAQ pour les featured snippets
        $optimized_faq = array();
        foreach ($generated_faq as $faq_item) {
            $optimized_faq[] = $this->optimize_faq_item_for_snippets($faq_item);
        }
        
        // Analyser et scorer chaque FAQ
        $analyzed_faq = array();
        foreach ($optimized_faq as $faq_item) {
            $analysis = $this->analyze_faq_item($faq_item);
            $faq_item['snippet_score'] = $analysis['snippet_score'];
            $faq_item['search_potential'] = $analysis['search_potential'];
            $faq_item['competition_level'] = $analysis['competition_level'];
            $analyzed_faq[] = $faq_item;
        }
        
        // Trier par potentiel de featured snippet
        usort($analyzed_faq, function($a, $b) {
            return $b['snippet_score'] <=> $a['snippet_score'];
        });
        
        // Sauvegarder les FAQ générées
        update_post_meta($post_id, '_quicky_auto_generated_faq', $analyzed_faq);
        update_post_meta($post_id, '_quicky_faq_generation_date', current_time('mysql'));
        
        return $analyzed_faq;
    }
    
    /**
     * GÉNÉRATION FAQ POUR RECETTES
     */
    private function generate_recipe_faq($title, $content_data, $post_content) {
        $faq_items = array();
        
        // Extraire les données clés de la recette
        $appliance = $this->extract_appliance_from_content($content_data, $post_content);
        $cooking_time = $this->extract_cooking_time($content_data);
        $difficulty = $this->extract_difficulty($content_data);
        $main_ingredient = $this->extract_main_ingredient($title, $content_data);
        
        // Template de questions pour recettes
        $recipe_question_templates = array(
            'cooking_time' => "How long does it take to make {recipe_name}?",
            'difficulty' => "Is {recipe_name} difficult to make?",
            'appliance_specific' => "Can I make {recipe_name} without {appliance}?",
            'storage' => "How do I store {recipe_name}?",
            'reheating' => "How do I reheat {recipe_name}?",
            'substitutions' => "What can I substitute for {main_ingredient} in {recipe_name}?",
            'serving_size' => "How many people does this {recipe_name} serve?",
            'make_ahead' => "Can I make {recipe_name} ahead of time?",
            'freezing' => "Can I freeze {recipe_name}?",
            'nutritional' => "How many calories are in {recipe_name}?",
            'troubleshooting' => "Why is my {recipe_name} not turning out right?",
            'variations' => "What are some variations of {recipe_name}?"
        );
        
        foreach ($recipe_question_templates as $type => $template) {
            $question = $this->populate_question_template($template, array(
                'recipe_name' => $this->extract_recipe_name($title),
                'appliance' => $appliance,
                'main_ingredient' => $main_ingredient
            ));
            
            $answer = $this->generate_answer_for_recipe_question($type, $content_data, $question);
            
            if ($answer) {
                $faq_items[] = array(
                    'question' => $question,
                    'answer' => $answer,
                    'type' => $type,
                    'priority' => $this->get_question_priority($type, 'recipe')
                );
            }
        }
        
        return $faq_items;
    }
    
    /**
     * GÉNÉRATION FAQ POUR GUIDES D'ACHAT
     */
    private function generate_buying_guide_faq($title, $content_data, $post_content) {
        $faq_items = array();
        
        $product_category = $this->extract_product_category($title, $content_data);
        $budget_ranges = $this->extract_budget_ranges($content_data);
        $top_pick = $this->extract_top_recommendation($content_data);
        
        $guide_question_templates = array(
            'best_overall' => "What is the best {product_category} in 2024?",
            'budget_friendly' => "What is the best budget {product_category}?",
            'premium_choice' => "What is the best premium {product_category}?",
            'buying_factors' => "What should I look for when buying a {product_category}?",
            'price_range' => "How much should I spend on a {product_category}?",
            'brand_recommendation' => "Which brand makes the best {product_category}?",
            'size_selection' => "What size {product_category} do I need?",
            'durability' => "How long does a {product_category} typically last?",
            'maintenance' => "How do I maintain my {product_category}?",
            'warranty' => "What warranty should I look for in a {product_category}?",
            'where_to_buy' => "Where is the best place to buy a {product_category}?",
            'timing' => "When is the best time to buy a {product_category}?"
        );
        
        foreach ($guide_question_templates as $type => $template) {
            $question = $this->populate_question_template($template, array(
                'product_category' => $product_category
            ));
            
            $answer = $this->generate_answer_for_guide_question($type, $content_data, $question, $top_pick);
            
            if ($answer) {
                $faq_items[] = array(
                    'question' => $question,
                    'answer' => $answer,
                    'type' => $type,
                    'priority' => $this->get_question_priority($type, 'buying-guide')
                );
            }
        }
        
        return $faq_items;
    }
    
    /**
     * GÉNÉRATION FAQ POUR COMPARATIFS
     */
    private function generate_comparison_faq($title, $content_data, $post_content) {
        $faq_items = array();
        
        $products = $this->extract_compared_products($title, $content_data);
        $winner = $this->extract_comparison_winner($content_data);
        $price_difference = $this->extract_price_difference($content_data);
        
        if (count($products) >= 2) {
            $product1 = $products[0];
            $product2 = $products[1];
            
            $comparison_question_templates = array(
                'which_better' => "Which is better, {product1} or {product2}?",
                'main_difference' => "What's the main difference between {product1} and {product2}?",
                'price_difference' => "Is {product1} worth the extra cost over {product2}?",
                'use_case_1' => "When should I choose {product1} over {product2}?",
                'use_case_2' => "When should I choose {product2} over {product1}?",
                'performance' => "Which performs better, {product1} or {product2}?",
                'value_for_money' => "Which offers better value, {product1} or {product2}?",
                'durability' => "Which lasts longer, {product1} or {product2}?",
                'ease_of_use' => "Which is easier to use, {product1} or {product2}?",
                'features' => "Which has more features, {product1} or {product2}?"
            );
            
            foreach ($comparison_question_templates as $type => $template) {
                $question = $this->populate_question_template($template, array(
                    'product1' => $product1,
                    'product2' => $product2
                ));
                
                $answer = $this->generate_answer_for_comparison_question($type, $content_data, $question, $winner);
                
                if ($answer) {
                    $faq_items[] = array(
                        'question' => $question,
                        'answer' => $answer,
                        'type' => $type,
                        'priority' => $this->get_question_priority($type, 'comparison')
                    );
                }
            }
        }
        
        return $faq_items;
    }
    
    /**
     * OPTIMISATION POUR FEATURED SNIPPETS
     */
    private function optimize_faq_item_for_snippets($faq_item) {
        $question = $faq_item['question'];
        $answer = $faq_item['answer'];
        
        // Optimiser la question
        $optimized_question = $this->optimize_question_for_snippets($question);
        
        // Optimiser la réponse
        $optimized_answer = $this->optimize_answer_for_snippets($answer);
        
        return array(
            'question' => $optimized_question,
            'answer' => $optimized_answer,
            'type' => $faq_item['type'] ?? '',
            'priority' => $faq_item['priority'] ?? 'medium',
            'optimization_applied' => true,
            'original_question' => $question,
            'original_answer' => $answer
        );
    }
    
    private function optimize_question_for_snippets($question) {
        // Patterns optimaux pour featured snippets
        $snippet_question_patterns = array(
            '/^What is/' => 'What is',
            '/^How to/' => 'How do you',
            '/^How long/' => 'How long does it take',
            '/^How much/' => 'How much does',
            '/^Why/' => 'Why does',
            '/^When/' => 'When should',
            '/^Where/' => 'Where can',
            '/^Which/' => 'Which is the best'
        );
        
        // Vérifier si la question suit déjà un pattern optimal
        foreach ($snippet_question_patterns as $pattern => $optimal) {
            if (preg_match($pattern, $question)) {
                return $question; // Déjà optimisée
            }
        }
        
        // Améliorer la structure de la question si nécessaire
        if (!preg_match('/^(What|How|Why|When|Where|Which|Who)/', $question)) {
            // Ajouter un mot interrogatif si manquant
            if (strpos($question, 'best') !== false) {
                $question = 'What is the ' . $question;
            } elseif (strpos($question, 'long') !== false) {
                $question = 'How ' . $question;
            }
        }
        
        return $question;
    }
    
    private function optimize_answer_for_snippets($answer) {
        // Longueur optimale pour featured snippets: 40-50 mots
        $words = str_word_count($answer);
        
        if ($words > 60) {
            // Raccourcir la réponse
            $sentences = preg_split('/(?<=[.!?])\s+/', $answer);
            $optimized_answer = '';
            $word_count = 0;
            
            foreach ($sentences as $sentence) {
                $sentence_words = str_word_count($sentence);
                if ($word_count + $sentence_words <= 50) {
                    $optimized_answer .= $sentence . ' ';
                    $word_count += $sentence_words;
                } else {
                    break;
                }
            }
            
            $answer = trim($optimized_answer);
        }
        
        // Structurer la réponse pour une meilleure lisibilité
        $answer = $this->improve_answer_structure($answer);
        
        // Ajouter des éléments qui performent bien dans les snippets
        $answer = $this->add_snippet_friendly_elements($answer);
        
        return $answer;
    }
    
    private function improve_answer_structure($answer) {
        // Commencer par une réponse directe si possible
        if (!preg_match('/^(Yes|No|The|It|You|To|For|In|On|At|With)/', $answer)) {
            // Ajouter une introduction directe si manquante
            if (strpos($answer, 'typically') !== false || strpos($answer, 'usually') !== false) {
                $answer = 'Typically, ' . lcfirst($answer);
            } elseif (strpos($answer, 'best') !== false) {
                $answer = 'The best ' . lcfirst($answer);
            }
        }
        
        // S'assurer que la réponse se termine par un point
        if (!preg_match('/[.!?]$/', $answer)) {
            $answer .= '.';
        }
        
        return $answer;
    }
    
    private function add_snippet_friendly_elements($answer) {
        // Ajouter des chiffres et données spécifiques si possible
        if (strpos($answer, 'minutes') !== false && !preg_match('/\d+/', $answer)) {
            $answer = str_replace('minutes', '20-30 minutes', $answer);
        }
        
        // Ajouter des qualificateurs de temps si approprié
        if (strpos($answer, 'cook') !== false || strpos($answer, 'bake') !== false) {
            if (!preg_match('/(today|currently|in 202[4-5])/', $answer)) {
                $answer = trim($answer, '.') . ' in 2024.';
            }
        }
        
        return $answer;
    }
    
    /**
     * ANALYSE DE PERFORMANCE FAQ
     */
    private function analyze_faq_item($faq_item) {
        $question = $faq_item['question'];
        $answer = $faq_item['answer'];
        
        $analysis = array(
            'snippet_score' => $this->calculate_snippet_score($question, $answer),
            'search_potential' => $this->estimate_search_potential($question),
            'competition_level' => $this->estimate_competition_level($question),
            'readability_score' => $this->calculate_faq_readability($answer),
            'length_optimization' => $this->analyze_length_optimization($answer),
            'keyword_relevance' => $this->analyze_keyword_relevance($question, $answer),
            'structure_score' => $this->analyze_answer_structure($answer)
        );
        
        return $analysis;
    }
    
    private function calculate_snippet_score($question, $answer) {
        $score = 0;
        
        // Points pour la structure de la question
        if (preg_match('/^(What|How|Why|When|Where|Which|Who)/', $question)) {
            $score += 25;
        }
        
        // Points pour la longueur de la réponse (40-50 mots optimal)
        $word_count = str_word_count($answer);
        if ($word_count >= 40 && $word_count <= 50) {
            $score += 30;
        } elseif ($word_count >= 30 && $word_count <= 60) {
            $score += 20;
        } elseif ($word_count >= 20 && $word_count <= 80) {
            $score += 10;
        }
        
        // Points pour la structure de la réponse
        if (preg_match('/^(Yes|No|The|To|You|It|For)/', $answer)) {
            $score += 20;
        }
        
        // Points pour les données spécifiques
        if (preg_match('/\d+/', $answer)) {
            $score += 15;
        }
        
        // Points pour la lisibilité
        $sentences = preg_split('/[.!?]+/', $answer);
        $avg_words_per_sentence = $word_count / max(1, count($sentences) - 1);
        if ($avg_words_per_sentence <= 15) {
            $score += 10;
        }
        
        return min(100, $score);
    }
    
    private function estimate_search_potential($question) {
        // Simuler l'estimation du potentiel de recherche
        $high_potential_patterns = array(
            '/how long/',
            '/how much/',
            '/what is the best/',
            '/how to/',
            '/why is/',
            '/when should/'
        );
        
        foreach ($high_potential_patterns as $pattern) {
            if (preg_match($pattern, strtolower($question))) {
                return 'High';
            }
        }
        
        $medium_potential_patterns = array(
            '/can i/',
            '/should i/',
            '/where/',
            '/which/',
            '/what/'
        );
        
        foreach ($medium_potential_patterns as $pattern) {
            if (preg_match($pattern, strtolower($question))) {
                return 'Medium';
            }
        }
        
        return 'Low';
    }
    
    private function estimate_competition_level($question) {
        // Simuler l'analyse de la concurrence
        $competitive_keywords = array('best', 'top', 'review', 'vs', 'comparison');
        $competition_score = 0;
        
        foreach ($competitive_keywords as $keyword) {
            if (stripos($question, $keyword) !== false) {
                $competition_score++;
            }
        }
        
        if ($competition_score >= 2) return 'High';
        if ($competition_score >= 1) return 'Medium';
        return 'Low';
    }
    
    /**
     * GÉNÉRATION DE RÉPONSES INTELLIGENTES
     */
    private function generate_answer_for_recipe_question($type, $content_data, $question) {
        switch ($type) {
            case 'cooking_time':
                return $this->generate_cooking_time_answer($content_data);
            case 'difficulty':
                return $this->generate_difficulty_answer($content_data);
            case 'appliance_specific':
                return $this->generate_appliance_alternative_answer($content_data);
            case 'storage':
                return $this->generate_storage_answer($content_data);
            case 'reheating':
                return $this->generate_reheating_answer($content_data);
            case 'substitutions':
                return $this->generate_substitution_answer($content_data);
            case 'serving_size':
                return $this->generate_serving_size_answer($content_data);
            case 'nutritional':
                return $this->generate_nutritional_answer($content_data);
            case 'troubleshooting':
                return $this->generate_troubleshooting_answer($content_data);
            default:
                return $this->generate_generic_recipe_answer($type, $content_data);
        }
    }
    
    private function generate_answer_for_guide_question($type, $content_data, $question, $top_pick) {
        switch ($type) {
            case 'best_overall':
                return $this->generate_best_overall_answer($content_data, $top_pick);
            case 'budget_friendly':
                return $this->generate_budget_answer($content_data);
            case 'premium_choice':
                return $this->generate_premium_answer($content_data);
            case 'buying_factors':
                return $this->generate_buying_factors_answer($content_data);
            case 'price_range':
                return $this->generate_price_range_answer($content_data);
            case 'durability':
                return $this->generate_durability_answer($content_data);
            default:
                return $this->generate_generic_guide_answer($type, $content_data);
        }
    }
    
    private function generate_answer_for_comparison_question($type, $content_data, $question, $winner) {
        switch ($type) {
            case 'which_better':
                return $this->generate_winner_answer($content_data, $winner);
            case 'main_difference':
                return $this->generate_main_difference_answer($content_data);
            case 'price_difference':
                return $this->generate_price_difference_answer($content_data);
            case 'performance':
                return $this->generate_performance_comparison_answer($content_data);
            case 'value_for_money':
                return $this->generate_value_comparison_answer($content_data);
            default:
                return $this->generate_generic_comparison_answer($type, $content_data);
        }
    }
    
    /**
     * IMPLÉMENTATIONS SPÉCIFIQUES DES GÉNÉRATEURS DE RÉPONSES
     */
    
    private function generate_cooking_time_answer($content_data) {
        $prep_time = $this->extract_time_value($content_data, 'prep_time');
        $cook_time = $this->extract_time_value($content_data, 'cook_time');
        $total_time = $prep_time + $cook_time;
        
        if ($total_time <= 30) {
            return "This recipe takes {$total_time} minutes total - {$prep_time} minutes prep and {$cook_time} minutes cooking. It's a quick and easy recipe perfect for busy weeknights.";
        } elseif ($total_time <= 60) {
            return "The total time is {$total_time} minutes, including {$prep_time} minutes of prep and {$cook_time} minutes of cooking. It's moderately quick to make.";
        } else {
            return "This recipe requires {$total_time} minutes total, with {$prep_time} minutes prep time and {$cook_time} minutes cooking time. Plan ahead for this more involved recipe.";
        }
    }
    
    private function generate_difficulty_answer($content_data) {
        $difficulty = $this->extract_difficulty($content_data);
        
        switch (strtolower($difficulty)) {
            case 'easy':
                return "This recipe is easy to make and perfect for beginners. The steps are straightforward and require basic cooking skills.";
            case 'medium':
                return "This recipe has medium difficulty and requires some cooking experience. Follow the instructions carefully for best results.";
            case 'hard':
                return "This is an advanced recipe that requires good cooking skills and attention to detail. Take your time with each step.";
            default:
                return "This recipe is suitable for home cooks with basic kitchen skills. The instructions are detailed to guide you through each step.";
        }
    }
    
    private function generate_storage_answer($content_data) {
        // Extraire les instructions de stockage si disponibles
        if (isset($content_data['main_content']['storage_and_meal_prep']['storage_instructions'])) {
            return $content_data['main_content']['storage_and_meal_prep']['storage_instructions'];
        }
        
        // Réponse générique basée sur le type de recette
        $recipe_type = $this->guess_recipe_type($content_data);
        
        switch ($recipe_type) {
            case 'baked_goods':
                return "Store in an airtight container at room temperature for up to 3 days, or refrigerate for up to 1 week.";
            case 'cooked_meal':
                return "Refrigerate leftovers in airtight containers for up to 3-4 days. Reheat thoroughly before serving.";
            case 'sauce_dressing':
                return "Store in the refrigerator in a sealed container for up to 1 week. Stir well before using.";
            default:
                return "Store leftovers in the refrigerator in airtight containers for 3-4 days for best quality and food safety.";
        }
    }
    
    private function generate_best_overall_answer($content_data, $top_pick) {
        if (isset($content_data['executive_summary']['top_pick_overall'])) {
            $top_pick_data = $content_data['executive_summary']['top_pick_overall'];
            $product_name = $top_pick_data['product_name'];
            $reason = $top_pick_data['reason'];
            
            return "The {$product_name} is our top pick because {$reason}. It offers the best combination of performance, features, and value.";
        }
        
        return "Based on our extensive testing, we recommend {$top_pick} for its superior performance and reliability in 2024.";
    }
    
    private function generate_budget_answer($content_data) {
        if (isset($content_data['executive_summary']['best_budget'])) {
            $budget_pick = $content_data['executive_summary']['best_budget'];
            $product_name = $budget_pick['product_name'];
            $reason = $budget_pick['reason'];
            
            return "For budget-conscious buyers, the {$product_name} offers the best value. {$reason}";
        }
        
        return "Our budget pick provides excellent value for money without sacrificing essential features.";
    }
    
    private function generate_winner_answer($content_data, $winner) {
        if (isset($content_data['final_verdict'])) {
            $verdict = $content_data['final_verdict'];
            $winner_name = $verdict['overall_winner'];
            $explanation = $verdict['victory_explanation'];
            
            return "{$winner_name} is the better choice overall. {$explanation}";
        }
        
        return "{$winner} performs better in most categories including performance, features, and overall value.";
    }
    
    /**
     * AJAX HANDLERS
     */
    
    public function ajax_generate_auto_faq() {
        if (!wp_verify_nonce($_POST['nonce'], 'quicky_meta_boxes_pro_nonce')) {
            wp_send_json_error('Invalid nonce');
        }
        
        $post_id = intval($_POST['post_id']);
        $content_type = get_post_meta($post_id, '_quicky_content_type', true);
        
        $generated_faq = $this->generate_automatic_faq($post_id, $content_type);
        
        wp_send_json_success(array(
            'faq_items' => $generated_faq,
            'message' => count($generated_faq) . ' FAQ items generated successfully'
        ));
    }
    
    public function ajax_optimize_faq_for_snippets() {
        if (!wp_verify_nonce($_POST['nonce'], 'quicky_meta_boxes_pro_nonce')) {
            wp_send_json_error('Invalid nonce');
        }
        
        $faq_data = $_POST['faq_data'];
        $optimized_faq = array();
        
        foreach ($faq_data as $faq_item) {
            $optimized_faq[] = $this->optimize_faq_item_for_snippets($faq_item);
        }
        
        wp_send_json_success(array(
            'optimized_faq' => $optimized_faq,
            'message' => 'FAQ optimized for featured snippets'
        ));
    }
    
    public function ajax_analyze_faq_performance() {
        if (!wp_verify_nonce($_POST['nonce'], 'quicky_meta_boxes_pro_nonce')) {
            wp_send_json_error('Invalid nonce');
        }
        
        $faq_data = $_POST['faq_data'];
        $analysis_results = array();
        
        foreach ($faq_data as $index => $faq_item) {
            $analysis_results[$index] = $this->analyze_faq_item($faq_item);
        }
        
        $overall_score = $this->calculate_overall_faq_score($analysis_results);
        
        wp_send_json_success(array(
            'analysis' => $analysis_results,
            'overall_score' => $overall_score,
            'recommendations' => $this->get_faq_improvement_recommendations($analysis_results)
        ));
    }
    
    public function ajax_get_faq_suggestions() {
        if (!wp_verify_nonce($_POST['nonce'], 'quicky_meta_boxes_pro_nonce')) {
            wp_send_json_error('Invalid nonce');
        }
        
        $post_id = intval($_POST['post_id']);
        $keyword = sanitize_text_field($_POST['keyword']);
        
        $suggestions = $this->get_faq_suggestions_for_keyword($keyword, $post_id);
        
        wp_send_json_success(array(
            'suggestions' => $suggestions,
            'message' => count($suggestions) . ' FAQ suggestions found'
        ));
    }
    
    /**
     * GÉNÉRATION AUTOMATIQUE LORS DE LA CRÉATION DE CONTENU
     */
    public function auto_generate_faq_on_content_creation($post_id, $content_data) {
        // Générer automatiquement les FAQ lors de la création de contenu enrichi
        $this->generate_automatic_faq($post_id, null, $content_data);
    }
    
    /**
     * MISE À JOUR DU SCHEMA FAQ
     */
    public function update_faq_schema_on_save($post_id) {
        if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
            return;
        }
        
        $faq_data = get_post_meta($post_id, '_quicky_faq_data', true);
        
        if ($faq_data) {
            $schema = $this->generate_faq_schema($faq_data);
            update_post_meta($post_id, '_quicky_faq_schema', json_encode($schema));
        }
    }
    
    private function generate_faq_schema($faq_data) {
        $faq_schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array()
        );
        
        foreach ($faq_data as $faq_item) {
            $faq_schema['mainEntity'][] = array(
                '@type' => 'Question',
                'name' => $faq_item['question'],
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text' => $faq_item['answer']
                )
            );
        }
        
        return $faq_schema;
    }
    
    /**
     * FONCTIONS UTILITAIRES
     */
    
    private function init_question_templates() {
        $this->question_templates = array(
            'recipe' => array(
                'time' => array('How long', 'How much time', 'What is the cooking time'),
                'difficulty' => array('How hard', 'How difficult', 'Is it easy'),
                'ingredients' => array('What ingredients', 'Can I substitute', 'What if I don\'t have'),
                'storage' => array('How to store', 'How long does it keep', 'Can I freeze'),
                'nutrition' => array('How many calories', 'Is it healthy', 'What is the nutritional'),
            ),
            'buying-guide' => array(
                'best' => array('What is the best', 'Which is the top', 'What do you recommend'),
                'budget' => array('What is the cheapest', 'Best budget option', 'Most affordable'),
                'features' => array('What features', 'What to look for', 'What should I consider'),
                'brand' => array('Which brand', 'Best manufacturer', 'Most reliable brand'),
            ),
            'comparison' => array(
                'winner' => array('Which is better', 'Who wins', 'What is the best choice'),
                'difference' => array('What is the difference', 'How do they compare', 'What sets them apart'),
                'price' => array('Which is cheaper', 'Is it worth the extra cost', 'Price difference'),
                'performance' => array('Which performs better', 'Performance comparison', 'Speed comparison'),
            )
        );
    }
    
    private function init_featured_snippet_patterns() {
        $this->featured_snippet_patterns = array(
            'definition' => '/^(What is|What are|Define)/',
            'how_to' => '/^(How to|How do|How can)/',
            'list' => '/^(List|Types of|Examples of)/',
            'comparison' => '/^(Difference between|Compare|vs)/',
            'best' => '/^(Best|Top|Recommended)/',
            'time' => '/^(How long|When|What time)/',
            'cost' => '/^(How much|Cost|Price)/',
            'location' => '/^(Where|Location)/'
        );
    }
    
    private function register_question_types() {
        // Enregistrer les types de questions avec leurs priorités
        $this->question_priorities = array(
            'recipe' => array(
                'cooking_time' => 'high',
                'difficulty' => 'high',
                'storage' => 'medium',
                'substitutions' => 'medium',
                'nutritional' => 'medium',
                'troubleshooting' => 'low',
                'variations' => 'low'
            ),
            'buying-guide' => array(
                'best_overall' => 'high',
                'budget_friendly' => 'high',
                'buying_factors' => 'high',
                'price_range' => 'medium',
                'brand_recommendation' => 'medium',
                'durability' => 'low',
                'warranty' => 'low'
            ),
            'comparison' => array(
                'which_better' => 'high',
                'main_difference' => 'high',
                'price_difference' => 'medium',
                'performance' => 'medium',
                'value_for_money' => 'medium',
                'use_case_1' => 'low',
                'use_case_2' => 'low'
            )
        );
    }
    
    private function get_question_priority($type, $content_type) {
        return $this->question_priorities[$content_type][$type] ?? 'medium';
    }
    
    private function populate_question_template($template, $variables) {
        $question = $template;
        
        foreach ($variables as $key => $value) {
            $question = str_replace('{' . $key . '}', $value, $question);
        }
        
        return $question;
    }
    
    // Méthodes d'extraction de données
    private function extract_appliance_from_content($content_data, $post_content) {
        if (isset($content_data['appliance_type'])) {
            return ucwords(str_replace('-', ' ', $content_data['appliance_type']));
        }
        
        $appliances = array('air fryer', 'instant pot', 'slow cooker', 'oven', 'microwave', 'stovetop');
        foreach ($appliances as $appliance) {
            if (stripos($post_content, $appliance) !== false) {
                return $appliance;
            }
        }
        
        return 'kitchen appliance';
    }
    
    private function extract_cooking_time($content_data) {
        return $content_data['cook_time'] ?? $content_data['total_time'] ?? 30;
    }
    
    private function extract_difficulty($content_data) {
        return $content_data['difficulty'] ?? 'medium';
    }
    
    private function extract_main_ingredient($title, $content_data) {
        // Essayer d'extraire l'ingrédient principal du titre
        $common_ingredients = array('chicken', 'beef', 'pork', 'fish', 'vegetables', 'pasta', 'rice', 'potatoes');
        
        foreach ($common_ingredients as $ingredient) {
            if (stripos($title, $ingredient) !== false) {
                return $ingredient;
            }
        }
        
        return 'main ingredient';
    }
    
    private function extract_recipe_name($title) {
        // Nettoyer le titre pour extraire le nom de la recette
        $cleaned = preg_replace('/\b(best|easy|quick|perfect|ultimate|amazing)\b/i', '', $title);
        $cleaned = preg_replace('/\b(recipe|for|with|in|using)\b.*$/i', '', $cleaned);
        return trim($cleaned);
    }
    
    private function extract_time_value($content_data, $time_type) {
        if (isset($content_data[$time_type])) {
            return intval($content_data[$time_type]);
        }
        
        // Valeurs par défaut
        $defaults = array(
            'prep_time' => 15,
            'cook_time' => 25,
            'total_time' => 40
        );
        
        return $defaults[$time_type] ?? 30;
    }
    
    private function guess_recipe_type($content_data) {
        $title = $content_data['title'] ?? '';
        
        if (preg_match('/\b(cake|cookies|bread|muffin|pie)\b/i', $title)) {
            return 'baked_goods';
        } elseif (preg_match('/\b(sauce|dressing|marinade)\b/i', $title)) {
            return 'sauce_dressing';
        } else {
            return 'cooked_meal';
        }
    }
    
    private function extract_product_category($title, $content_data) {
        // Extraire la catégorie de produit du titre ou des données
        if (isset($content_data['product_category'])) {
            return $content_data['product_category'];
        }
        
        $categories = array('air fryer', 'blender', 'food processor', 'mixer', 'toaster', 'coffee maker');
        foreach ($categories as $category) {
            if (stripos($title, $category) !== false) {
                return $category;
            }
        }
        
        return 'kitchen appliance';
    }
    
    private function extract_budget_ranges($content_data) {
        // Extraire les gammes de prix des données
        $ranges = array();
        
        if (isset($content_data['executive_summary'])) {
            $summary = $content_data['executive_summary'];
            if (isset($summary['best_budget']['price_point'])) {
                $ranges['budget'] = $summary['best_budget']['price_point'];
            }
            if (isset($summary['best_premium']['price_point'])) {
                $ranges['premium'] = $summary['best_premium']['price_point'];
            }
        }
        
        return $ranges;
    }
    
    private function extract_top_recommendation($content_data) {
        if (isset($content_data['executive_summary']['top_pick_overall']['product_name'])) {
            return $content_data['executive_summary']['top_pick_overall']['product_name'];
        }
        
        return 'our top pick';
    }
    
    private function extract_compared_products($title, $content_data) {
        // Extraire les produits comparés du titre ou des données
        if (isset($content_data['products_overview'])) {
            return array_map(function($product) {
                return $product['product_name'] ?? $product['name'] ?? 'Product';
            }, $content_data['products_overview']);
        }
        
        // Essayer d'extraire du titre (ex: "Product A vs Product B")
        if (preg_match('/(.+?)\s+vs\s+(.+)/i', $title, $matches)) {
            return array(trim($matches[1]), trim($matches[2]));
        }
        
        return array('Product A', 'Product B');
    }
    
    private function extract_comparison_winner($content_data) {
        if (isset($content_data['final_verdict']['overall_winner'])) {
            return $content_data['final_verdict']['overall_winner'];
        }
        
        return 'the winner';
    }
    
    private function extract_price_difference($content_data) {
        // Calculer la différence de prix si disponible
        if (isset($content_data['products_overview']) && count($content_data['products_overview']) >= 2) {
            $product1_price = $this->extract_price_from_text($content_data['products_overview'][0]['current_price'] ?? '');
            $product2_price = $this->extract_price_from_text($content_data['products_overview'][1]['current_price'] ?? '');
            
            if ($product1_price && $product2_price) {
                return abs($product1_price - $product2_price);
            }
        }
        
        return null;
    }
    
    private function extract_price_from_text($price_text) {
        if (preg_match('/\$(\d+(?:\.\d{2})?)/', $price_text, $matches)) {
            return floatval($matches[1]);
        }
        return null;
    }
    
    // Générateurs de réponses génériques
    private function generate_generic_recipe_answer($type, $content_data) {
        $generic_answers = array(
            'make_ahead' => "Yes, you can prepare parts of this recipe in advance. Check the storage instructions for specific details.",
            'freezing' => "This recipe can typically be frozen for up to 3 months. Wrap well and label with the date.",
            'variations' => "There are several ways to customize this recipe. Try different seasonings or substitute ingredients based on your preferences."
        );
        
        return $generic_answers[$type] ?? null;
    }
    
    private function generate_generic_guide_answer($type, $content_data) {
        $generic_answers = array(
            'maintenance' => "Regular cleaning and proper care will extend the life of your appliance. Follow manufacturer instructions.",
            'warranty' => "Look for products with at least a 1-year warranty. Extended warranties may be worth considering for expensive items.",
            'where_to_buy' => "You can find these products at major retailers, online stores, and specialty kitchen shops."
        );
        
        return $generic_answers[$type] ?? null;
    }
    
    private function generate_generic_comparison_answer($type, $content_data) {
        $generic_answers = array(
            'durability' => "Both products are built to last, but build quality and materials can vary. Check warranty terms for confidence.",
            'ease_of_use' => "Both options are user-friendly, but they may have different learning curves depending on your experience.",
            'features' => "Each product offers unique features. Consider which features matter most for your specific needs."
        );
        
        return $generic_answers[$type] ?? null;
    }
    
    // Méthodes d'analyse supplémentaires
    private function calculate_faq_readability($answer) {
        $sentences = preg_split('/[.!?]+/', $answer);
        $words = str_word_count($answer);
        $sentences_count = count($sentences) - 1; // Retirer la dernière entrée vide
        
        if ($sentences_count === 0) return 50;
        
        $avg_words_per_sentence = $words / $sentences_count;
        
        if ($avg_words_per_sentence <= 15) return 90;
        if ($avg_words_per_sentence <= 20) return 75;
        if ($avg_words_per_sentence <= 25) return 60;
        return 40;
    }
    
    private function analyze_length_optimization($answer) {
        $word_count = str_word_count($answer);
        
        if ($word_count >= 40 && $word_count <= 50) {
            return array('status' => 'optimal', 'score' => 100);
        } elseif ($word_count >= 30 && $word_count <= 60) {
            return array('status' => 'good', 'score' => 80);
        } elseif ($word_count >= 20 && $word_count <= 80) {
            return array('status' => 'acceptable', 'score' => 60);
        } else {
            return array('status' => 'needs_optimization', 'score' => 40);
        }
    }
    
    private function analyze_keyword_relevance($question, $answer) {
        // Extraire les mots-clés principaux de la question
        $question_words = array_filter(str_word_count(strtolower($question), 1), function($word) {
            return strlen($word) > 3 && !in_array($word, array('what', 'how', 'when', 'where', 'why', 'which', 'that', 'this', 'with', 'from'));
        });
        
        $answer_lower = strtolower($answer);
        $relevance_score = 0;
        
        foreach ($question_words as $word) {
            if (strpos($answer_lower, $word) !== false) {
                $relevance_score += 20;
            }
        }
        
        return min(100, $relevance_score);
    }
    
    private function analyze_answer_structure($answer) {
        $score = 50; // Base score
        
        // Points pour commencer par une réponse directe
        if (preg_match('/^(Yes|No|The|To|You|It|For|In|On|At|With)/', $answer)) {
            $score += 25;
        }
        
        // Points pour finir par un point
        if (preg_match('/[.!]$/', $answer)) {
            $score += 15;
        }
        
        // Points pour structure claire
        if (preg_match('/\d+/', $answer)) {
            $score += 10; // Données spécifiques
        }
        
        return min(100, $score);
    }
    
    private function calculate_overall_faq_score($analysis_results) {
        if (empty($analysis_results)) return 0;
        
        $total_score = 0;
        $count = 0;
        
        foreach ($analysis_results as $analysis) {
            $total_score += $analysis['snippet_score'];
            $count++;
        }
        
        return $count > 0 ? round($total_score / $count, 1) : 0;
    }
    
    private function get_faq_improvement_recommendations($analysis_results) {
        $recommendations = array();
        
        foreach ($analysis_results as $index => $analysis) {
            if ($analysis['snippet_score'] < 70) {
                $recommendations[] = "FAQ #{$index}: Optimize for featured snippets (current score: {$analysis['snippet_score']})";
            }
            
            if (isset($analysis['length_optimization']['status']) && $analysis['length_optimization']['status'] === 'needs_optimization') {
                $recommendations[] = "FAQ #{$index}: Adjust answer length for optimal snippet performance";
            }
            
            if ($analysis['readability_score'] < 70) {
                $recommendations[] = "FAQ #{$index}: Improve readability with shorter sentences";
            }
        }
        
        return $recommendations;
    }
    
    private function get_faq_suggestions_for_keyword($keyword, $post_id) {
        // Simuler la génération de suggestions basées sur un mot-clé
        $suggestions = array();
        $content_type = get_post_meta($post_id, '_quicky_content_type', true);
        
        if ($content_type === 'recipe') {
            $suggestions = array(
                "How long to cook {$keyword}?",
                "What temperature for {$keyword}?",
                "How to store {$keyword}?",
                "Can I freeze {$keyword}?",
                "How many calories in {$keyword}?"
            );
        } elseif ($content_type === 'buying-guide') {
            $suggestions = array(
                "What is the best {$keyword}?",
                "How much does {$keyword} cost?",
                "Where to buy {$keyword}?",
                "What to look for in {$keyword}?",
                "Which {$keyword} brand is best?"
            );
        }
        
        return $suggestions;
    }
    
    private function init_content_analysis_patterns() {
        // Initialiser les patterns d'analyse de contenu
        $this->content_patterns = array(
            'time_mentions' => '/(\d+)\s*(minutes?|hours?|days?)/',
            'price_mentions' => '/\$(\d+(?:\.\d{2})?)/',
            'quantity_mentions' => '/(\d+)\s*(cups?|tablespoons?|teaspoons?)/',
            'temperature_mentions' => '/(\d+)\s*°?[FC]?/',
            'rating_mentions' => '/(\d+(?:\.\d)?)\s*(?:out of|\/)\s*(\d+)/'
        );
    }
    
    private function load_faq_analytics_data() {
        // Charger les données d'analytics FAQ (placeholder)
        $this->faq_analytics = array(
            'top_performing_questions' => array(),
            'search_volume_data' => array(),
            'competition_analysis' => array(),
            'snippet_win_rate' => 0
        );
    }
}

// Initialisation
new QuickyFAQManager();