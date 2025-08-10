<?php
// includes/class-quicky-content-analyzer.php

if (!defined('ABSPATH')) {
    exit;
}

class QuickyContentAnalyzer {
    
    private $readability_scores = [];
    private $seo_scores = [];
    private $quality_metrics = [];
    
    public function __construct() {
        add_action('save_post', array($this, 'analyze_content_on_save'), 20, 2);
        add_action('wp_ajax_analyze_content_real_time', array($this, 'ajax_analyze_content_real_time'));
        add_action('wp_ajax_get_content_suggestions', array($this, 'ajax_get_content_suggestions'));
        add_action('wp_ajax_analyze_competitor_content', array($this, 'ajax_analyze_competitor_content'));
        
        // Hook pour affichage en temps réel dans l'éditeur
        add_action('admin_footer', array($this, 'add_real_time_analyzer_script'));
    }
    
    /**
     * ANALYSE COMPLÈTE DU CONTENU
     */
    public function analyze_content_comprehensive($post_id, $content = null) {
        if (!$content) {
            $content = get_post_field('post_content', $post_id);
        }
        
        $analysis = array(
            'overall_score' => 0,
            'readability' => $this->analyze_readability($content),
            'seo_optimization' => $this->analyze_seo_optimization($post_id, $content),
            'content_quality' => $this->analyze_content_quality($content),
            'structure_analysis' => $this->analyze_content_structure($content),
            'keyword_analysis' => $this->analyze_keyword_optimization($post_id, $content),
            'competitor_analysis' => $this->analyze_vs_competitors($post_id),
            'improvement_suggestions' => array(),
            'strengths' => array(),
            'weaknesses' => array(),
            'action_items' => array()
        );
        
        // Calculer le score global
        $analysis['overall_score'] = $this->calculate_overall_score($analysis);
        
        // Générer suggestions d'amélioration
        $analysis['improvement_suggestions'] = $this->generate_improvement_suggestions($analysis);
        
        // Identifier forces et faiblesses
        $analysis['strengths'] = $this->identify_content_strengths($analysis);
        $analysis['weaknesses'] = $this->identify_content_weaknesses($analysis);
        
        // Générer actions concrètes
        $analysis['action_items'] = $this->generate_action_items($analysis);
        
        // Sauvegarder l'analyse
        update_post_meta($post_id, '_quicky_content_analysis', $analysis);
        update_post_meta($post_id, '_quicky_analysis_date', current_time('mysql'));
        
        return $analysis;
    }
    
    /**
     * ANALYSE DE LISIBILITÉ AVANCÉE
     */
    private function analyze_readability($content) {
        $clean_content = strip_tags($content);
        $sentences = $this->count_sentences($clean_content);
        $words = str_word_count($clean_content);
        $syllables = $this->count_syllables($clean_content);
        $paragraphs = $this->count_paragraphs($content);
        
        // Calcul du Flesch Reading Ease Score
        $flesch_score = 206.835 - (1.015 * ($words / $sentences)) - (84.6 * ($syllables / $words));
        $flesch_score = max(0, min(100, $flesch_score));
        
        // Calcul du Flesch-Kincaid Grade Level
        $fk_grade = (0.39 * ($words / $sentences)) + (11.8 * ($syllables / $words)) - 15.59;
        $fk_grade = max(1, $fk_grade);
        
        // Analyse des phrases
        $sentence_analysis = $this->analyze_sentence_structure($content);
        
        // Analyse du vocabulaire
        $vocabulary_analysis = $this->analyze_vocabulary_complexity($clean_content);
        
        return array(
            'flesch_score' => round($flesch_score, 1),
            'flesch_level' => $this->get_flesch_level($flesch_score),
            'fk_grade' => round($fk_grade, 1),
            'sentences' => $sentences,
            'words' => $words,
            'syllables' => $syllables,
            'paragraphs' => $paragraphs,
            'avg_words_per_sentence' => round($words / max(1, $sentences), 1),
            'avg_sentences_per_paragraph' => round($sentences / max(1, $paragraphs), 1),
            'sentence_analysis' => $sentence_analysis,
            'vocabulary_analysis' => $vocabulary_analysis,
            'readability_recommendations' => $this->get_readability_recommendations($flesch_score, $fk_grade, $sentence_analysis)
        );
    }
    
    /**
     * ANALYSE SEO AVANCÉE
     */
    private function analyze_seo_optimization($post_id, $content) {
        $title = get_the_title($post_id);
        $meta_description = get_post_meta($post_id, '_yoast_wpseo_metadesc', true);
        $focus_keyword = get_post_meta($post_id, '_yoast_wpseo_focuskw', true);
        
        $seo_analysis = array(
            'title_analysis' => $this->analyze_title_seo($title, $focus_keyword),
            'meta_description_analysis' => $this->analyze_meta_description($meta_description, $focus_keyword),
            'content_seo' => $this->analyze_content_seo($content, $focus_keyword),
            'heading_structure' => $this->analyze_heading_structure($content),
            'internal_links' => $this->analyze_internal_links($content),
            'external_links' => $this->analyze_external_links($content),
            'image_optimization' => $this->analyze_image_optimization($content),
            'schema_markup' => $this->analyze_schema_markup($post_id),
            'url_analysis' => $this->analyze_url_structure($post_id),
            'keyword_density' => $this->calculate_keyword_density($content, $focus_keyword),
            'semantic_keywords' => $this->find_semantic_keywords($content, $focus_keyword),
            'competitor_gap_analysis' => $this->analyze_content_gaps($post_id, $focus_keyword)
        );
        
        // Score SEO global
        $seo_analysis['overall_seo_score'] = $this->calculate_seo_score($seo_analysis);
        
        return $seo_analysis;
    }
    
    /**
     * ANALYSE DE QUALITÉ DU CONTENU
     */
    private function analyze_content_quality($content) {
        $quality_metrics = array(
            'content_depth' => $this->analyze_content_depth($content),
            'expertise_signals' => $this->detect_expertise_signals($content),
            'trustworthiness_indicators' => $this->detect_trustworthiness_indicators($content),
            'freshness_score' => $this->analyze_content_freshness($content),
            'uniqueness_score' => $this->analyze_content_uniqueness($content),
            'engagement_potential' => $this->predict_engagement_potential($content),
            'emotional_impact' => $this->analyze_emotional_impact($content),
            'actionability_score' => $this->analyze_actionability($content),
            'comprehensiveness' => $this->analyze_comprehensiveness($content)
        );
        
        $quality_metrics['overall_quality_score'] = $this->calculate_quality_score($quality_metrics);
        
        return $quality_metrics;
    }
    
    /**
     * ANALYSE DE STRUCTURE DU CONTENU
     */
    private function analyze_content_structure($content) {
        return array(
            'heading_hierarchy' => $this->analyze_heading_hierarchy($content),
            'paragraph_analysis' => $this->analyze_paragraph_structure($content),
            'list_usage' => $this->analyze_list_usage($content),
            'visual_elements' => $this->analyze_visual_elements($content),
            'content_flow' => $this->analyze_content_flow($content),
            'call_to_action_analysis' => $this->analyze_cta_elements($content),
            'table_analysis' => $this->analyze_table_usage($content),
            'code_block_analysis' => $this->analyze_code_blocks($content),
            'quote_analysis' => $this->analyze_quote_usage($content)
        );
    }
    
    /**
     * ANALYSE D'OPTIMISATION DES MOTS-CLÉS
     */
    private function analyze_keyword_optimization($post_id, $content) {
        $focus_keyword = get_post_meta($post_id, '_yoast_wpseo_focuskw', true);
        $title = get_the_title($post_id);
        
        if (!$focus_keyword) {
            return array(
                'focus_keyword_set' => false,
                'recommendation' => 'Set a focus keyword for detailed analysis'
            );
        }
        
        return array(
            'focus_keyword_set' => true,
            'focus_keyword' => $focus_keyword,
            'keyword_in_title' => $this->check_keyword_in_title($title, $focus_keyword),
            'keyword_in_content' => $this->analyze_keyword_in_content($content, $focus_keyword),
            'keyword_density' => $this->calculate_keyword_density($content, $focus_keyword),
            'keyword_distribution' => $this->analyze_keyword_distribution($content, $focus_keyword),
            'long_tail_variations' => $this->find_long_tail_variations($content, $focus_keyword),
            'semantic_keywords' => $this->find_semantic_keywords($content, $focus_keyword),
            'keyword_prominence' => $this->calculate_keyword_prominence($content, $focus_keyword),
            'related_keywords_found' => $this->find_related_keywords($content, $focus_keyword),
            'keyword_stuffing_check' => $this->check_keyword_stuffing($content, $focus_keyword),
            'lsi_keywords' => $this->find_lsi_keywords($content, $focus_keyword)
        );
    }
    
    /**
     * ANALYSE CONCURRENTIELLE
     */
    private function analyze_vs_competitors($post_id) {
        $focus_keyword = get_post_meta($post_id, '_yoast_wpseo_focuskw', true);
        
        if (!$focus_keyword) {
            return array('analysis_available' => false);
        }
        
        // Simuler une analyse concurrentielle (en production, cela ferait des requêtes API)
        return array(
            'analysis_available' => true,
            'competitor_count' => rand(8, 15),
            'avg_competitor_word_count' => rand(1500, 3500),
            'your_word_count' => str_word_count(strip_tags(get_post_field('post_content', $post_id))),
            'content_gap_analysis' => $this->simulate_content_gaps($focus_keyword),
            'competitive_strength' => $this->assess_competitive_strength($post_id),
            'opportunities' => $this->identify_competitive_opportunities($focus_keyword),
            'threats' => $this->identify_competitive_threats($focus_keyword),
            'recommended_improvements' => $this->get_competitive_improvement_suggestions($post_id)
        );
    }
    
    /**
     * CALCUL DU SCORE GLOBAL
     */
    private function calculate_overall_score($analysis) {
        $weights = array(
            'readability' => 0.20,
            'seo_optimization' => 0.30,
            'content_quality' => 0.35,
            'structure_analysis' => 0.15
        );
        
        $score = 0;
        $score += ($analysis['readability']['flesch_score'] / 100) * $weights['readability'] * 100;
        $score += ($analysis['seo_optimization']['overall_seo_score'] / 100) * $weights['seo_optimization'] * 100;
        $score += ($analysis['content_quality']['overall_quality_score'] / 100) * $weights['content_quality'] * 100;
        $score += $this->calculate_structure_score($analysis['structure_analysis']) * $weights['structure_analysis'];
        
        return round(min(100, max(0, $score)), 1);
    }
    
    /**
     * GÉNÉRATION DE SUGGESTIONS D'AMÉLIORATION
     */
    private function generate_improvement_suggestions($analysis) {
        $suggestions = array();
        
        // Suggestions de lisibilité
        if ($analysis['readability']['flesch_score'] < 60) {
            $suggestions[] = array(
                'category' => 'readability',
                'priority' => 'high',
                'title' => 'Improve Readability',
                'description' => 'Your content is difficult to read. Consider using shorter sentences and simpler words.',
                'action' => 'Break long sentences into shorter ones and replace complex words with simpler alternatives.'
            );
        }
        
        // Suggestions SEO
        if ($analysis['seo_optimization']['overall_seo_score'] < 70) {
            $suggestions[] = array(
                'category' => 'seo',
                'priority' => 'high',
                'title' => 'Optimize for SEO',
                'description' => 'Your SEO optimization can be improved.',
                'action' => 'Add more focus keyword mentions, optimize headings, and improve internal linking.'
            );
        }
        
        // Suggestions de qualité
        if ($analysis['content_quality']['overall_quality_score'] < 75) {
            $suggestions[] = array(
                'category' => 'quality',
                'priority' => 'medium',
                'title' => 'Enhance Content Quality',
                'description' => 'Add more depth and expertise signals to your content.',
                'action' => 'Include more detailed examples, statistics, and expert insights.'
            );
        }
        
        // Suggestions de structure
        $structure_score = $this->calculate_structure_score($analysis['structure_analysis']);
        if ($structure_score < 70) {
            $suggestions[] = array(
                'category' => 'structure',
                'priority' => 'medium',
                'title' => 'Improve Content Structure',
                'description' => 'Better organize your content with clear headings and formatting.',
                'action' => 'Add more subheadings, use bullet points, and include visual elements.'
            );
        }
        
        return $suggestions;
    }
    
    /**
     * ANALYSE EN TEMPS RÉEL (AJAX)
     */
    public function ajax_analyze_content_real_time() {
        if (!wp_verify_nonce($_POST['nonce'], 'quicky_analyzer_nonce')) {
            wp_send_json_error('Invalid nonce');
        }
        
        $content = wp_kses_post($_POST['content']);
        $post_id = intval($_POST['post_id']);
        
        // Analyse rapide pour l'affichage en temps réel
        $quick_analysis = array(
            'word_count' => str_word_count(strip_tags($content)),
            'readability_score' => $this->quick_readability_check($content),
            'seo_score' => $this->quick_seo_check($post_id, $content),
            'structure_score' => $this->quick_structure_check($content),
            'suggestions' => $this->get_quick_suggestions($content)
        );
        
        wp_send_json_success($quick_analysis);
    }
    
    /**
     * AJAX POUR SUGGESTIONS D'AMÉLIORATION
     */
    public function ajax_get_content_suggestions() {
        if (!wp_verify_nonce($_POST['nonce'], 'quicky_analyzer_nonce')) {
            wp_send_json_error('Invalid nonce');
        }
        
        $post_id = intval($_POST['post_id']);
        $suggestion_type = sanitize_text_field($_POST['suggestion_type']);
        
        $suggestions = $this->get_specific_suggestions($post_id, $suggestion_type);
        
        wp_send_json_success($suggestions);
    }
    
    /**
     * ANALYSE CONCURRENTIELLE AJAX
     */
    public function ajax_analyze_competitor_content() {
        if (!wp_verify_nonce($_POST['nonce'], 'quicky_analyzer_nonce')) {
            wp_send_json_error('Invalid nonce');
        }
        
        $keyword = sanitize_text_field($_POST['keyword']);
        $post_id = intval($_POST['post_id']);
        
        $competitor_analysis = $this->perform_competitor_analysis($keyword, $post_id);
        
        wp_send_json_success($competitor_analysis);
    }
    
    /**
     * SCRIPT D'ANALYSE EN TEMPS RÉEL
     */
    public function add_real_time_analyzer_script() {
        global $pagenow;
        
        if (!in_array($pagenow, ['post.php', 'post-new.php'])) {
            return;
        }
        
        ?>
        <script>
        jQuery(document).ready(function($) {
            let analysisTimer;
            const $contentAnalyzer = $('<div id="quicky-content-analyzer" style="position: fixed; top: 50px; right: 20px; width: 300px; background: white; border: 1px solid #ddd; border-radius: 8px; padding: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); z-index: 10000; display: none;"><h4>📊 Content Analysis</h4><div id="analysis-content"></div></div>');
            
            $('body').append($contentAnalyzer);
            
            // Analyser le contenu en temps réel
            function analyzeContentRealTime() {
                const content = wp.editor && wp.editor.getContent ? wp.editor.getContent() : $('#content').val();
                const postId = $('#post_ID').val();
                
                if (!content || content.length < 100) return;
                
                $.post(ajaxurl, {
                    action: 'analyze_content_real_time',
                    nonce: '<?php echo wp_create_nonce('quicky_analyzer_nonce'); ?>',
                    content: content,
                    post_id: postId
                }, function(response) {
                    if (response.success) {
                        updateAnalysisDisplay(response.data);
                    }
                });
            }
            
            function updateAnalysisDisplay(data) {
                const html = `
                    <div class="analysis-metrics">
                        <div class="metric">
                            <span class="label">Words:</span>
                            <span class="value">${data.word_count}</span>
                        </div>
                        <div class="metric">
                            <span class="label">Readability:</span>
                            <span class="value score-${getScoreClass(data.readability_score)}">${data.readability_score}%</span>
                        </div>
                        <div class="metric">
                            <span class="label">SEO:</span>
                            <span class="value score-${getScoreClass(data.seo_score)}">${data.seo_score}%</span>
                        </div>
                        <div class="metric">
                            <span class="label">Structure:</span>
                            <span class="value score-${getScoreClass(data.structure_score)}">${data.structure_score}%</span>
                        </div>
                    </div>
                    ${data.suggestions.length ? '<div class="quick-suggestions"><h5>Quick Tips:</h5>' + data.suggestions.map(s => '<div class="suggestion">' + s + '</div>').join('') + '</div>' : ''}
                `;
                
                $('#analysis-content').html(html);
                $('#quicky-content-analyzer').show();
            }
            
            function getScoreClass(score) {
                if (score >= 80) return 'excellent';
                if (score >= 60) return 'good';
                if (score >= 40) return 'average';
                return 'poor';
            }
            
            // Déclencher l'analyse après 2 secondes d'inactivité
            $(document).on('input keyup', '#content, .wp-editor-area', function() {
                clearTimeout(analysisTimer);
                analysisTimer = setTimeout(analyzeContentRealTime, 2000);
            });
            
            // Toggle analyzer
            $(document).on('keydown', function(e) {
                if (e.ctrlKey && e.shiftKey && e.keyCode === 65) { // Ctrl+Shift+A
                    $('#quicky-content-analyzer').toggle();
                }
            });
        });
        </script>
        
        <style>
        #quicky-content-analyzer .analysis-metrics {
            margin-bottom: 15px;
        }
        
        #quicky-content-analyzer .metric {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        
        #quicky-content-analyzer .label {
            font-weight: 600;
        }
        
        #quicky-content-analyzer .value {
            font-weight: bold;
        }
        
        #quicky-content-analyzer .score-excellent { color: #46b450; }
        #quicky-content-analyzer .score-good { color: #ffb900; }
        #quicky-content-analyzer .score-average { color: #ff8c00; }
        #quicky-content-analyzer .score-poor { color: #dc3232; }
        
        #quicky-content-analyzer .quick-suggestions h5 {
            margin: 10px 0 5px 0;
            font-size: 12px;
            text-transform: uppercase;
            color: #666;
        }
        
        #quicky-content-analyzer .suggestion {
            font-size: 11px;
            background: #f8f9fa;
            padding: 5px 8px;
            margin-bottom: 5px;
            border-radius: 4px;
        }
        </style>
        <?php
    }
    
    /**
     * ANALYSE AUTOMATIQUE À LA SAUVEGARDE
     */
    public function analyze_content_on_save($post_id, $post) {
        if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Lancer l'analyse complète en arrière-plan
        wp_schedule_single_event(time(), 'quicky_analyze_content_background', array($post_id));
    }
    
    /**
     * FONCTIONS UTILITAIRES D'ANALYSE
     */
    
    private function count_sentences($text) {
        return preg_match_all('/[.!?]+/', $text);
    }
    
    private function count_syllables($text) {
        $words = str_word_count(strtolower($text), 1);
        $syllables = 0;
        
        foreach ($words as $word) {
            $syllables += max(1, preg_match_all('/[aeiouy]/', $word));
        }
        
        return $syllables;
    }
    
    private function count_paragraphs($content) {
        return substr_count($content, '</p>') + substr_count($content, '<br>');
    }
    
    private function get_flesch_level($score) {
        if ($score >= 90) return 'Very Easy';
        if ($score >= 80) return 'Easy';
        if ($score >= 70) return 'Fairly Easy';
        if ($score >= 60) return 'Standard';
        if ($score >= 50) return 'Fairly Difficult';
        if ($score >= 30) return 'Difficult';
        return 'Very Difficult';
    }
    
    private function analyze_sentence_structure($content) {
        $sentences = preg_split('/[.!?]+/', strip_tags($content));
        $sentences = array_filter($sentences);
        
        $short_sentences = 0;
        $medium_sentences = 0;
        $long_sentences = 0;
        $very_long_sentences = 0;
        
        foreach ($sentences as $sentence) {
            $word_count = str_word_count($sentence);
            if ($word_count <= 10) $short_sentences++;
            elseif ($word_count <= 20) $medium_sentences++;
            elseif ($word_count <= 30) $long_sentences++;
            else $very_long_sentences++;
        }
        
        return array(
            'total_sentences' => count($sentences),
            'short_sentences' => $short_sentences,
            'medium_sentences' => $medium_sentences,
            'long_sentences' => $long_sentences,
            'very_long_sentences' => $very_long_sentences,
            'sentence_variety_score' => $this->calculate_sentence_variety_score($short_sentences, $medium_sentences, $long_sentences, $very_long_sentences)
        );
    }
    
    private function analyze_vocabulary_complexity($text) {
        $words = str_word_count(strtolower($text), 1);
        $unique_words = array_unique($words);
        $word_lengths = array_map('strlen', $words);
        
        $complex_words = 0;
        foreach ($words as $word) {
            if (strlen($word) > 6) $complex_words++;
        }
        
        return array(
            'total_words' => count($words),
            'unique_words' => count($unique_words),
            'vocabulary_diversity' => round(count($unique_words) / count($words) * 100, 1),
            'average_word_length' => round(array_sum($word_lengths) / count($word_lengths), 1),
            'complex_words' => $complex_words,
            'complex_word_percentage' => round($complex_words / count($words) * 100, 1)
        );
    }
    
    private function get_readability_recommendations($flesch_score, $fk_grade, $sentence_analysis) {
        $recommendations = array();
        
        if ($flesch_score < 60) {
            $recommendations[] = 'Use shorter sentences to improve readability';
            $recommendations[] = 'Replace complex words with simpler alternatives';
        }
        
        if ($fk_grade > 12) {
            $recommendations[] = 'Lower the reading grade level for broader audience appeal';
        }
        
        if ($sentence_analysis['very_long_sentences'] > $sentence_analysis['total_sentences'] * 0.2) {
            $recommendations[] = 'Break down very long sentences (30+ words)';
        }
        
        if ($sentence_analysis['sentence_variety_score'] < 60) {
            $recommendations[] = 'Vary sentence lengths for better flow';
        }
        
        return $recommendations;
    }
    
    private function analyze_title_seo($title, $focus_keyword) {
        $title_length = strlen($title);
        $keyword_in_title = !empty($focus_keyword) && stripos($title, $focus_keyword) !== false;
        $keyword_position = $keyword_in_title ? stripos($title, $focus_keyword) : null;
        
        return array(
            'title_length' => $title_length,
            'length_optimal' => $title_length >= 50 && $title_length <= 60,
            'keyword_present' => $keyword_in_title,
            'keyword_position' => $keyword_position,
            'keyword_at_beginning' => $keyword_position !== null && $keyword_position <= 10,
            'title_score' => $this->calculate_title_score($title_length, $keyword_in_title, $keyword_position)
        );
    }
    
    private function analyze_meta_description($meta_description, $focus_keyword) {
        if (empty($meta_description)) {
            return array(
                'present' => false,
                'recommendation' => 'Add a meta description'
            );
        }
        
        $desc_length = strlen($meta_description);
        $keyword_in_desc = !empty($focus_keyword) && stripos($meta_description, $focus_keyword) !== false;
        
        return array(
            'present' => true,
            'length' => $desc_length,
            'length_optimal' => $desc_length >= 150 && $desc_length <= 160,
            'keyword_present' => $keyword_in_desc,
            'call_to_action' => $this->detect_call_to_action($meta_description),
            'description_score' => $this->calculate_description_score($desc_length, $keyword_in_desc)
        );
    }
    
    private function analyze_content_seo($content, $focus_keyword) {
        if (empty($focus_keyword)) {
            return array('analysis_available' => false);
        }
        
        $clean_content = strip_tags($content);
        $word_count = str_word_count($clean_content);
        $keyword_count = substr_count(strtolower($clean_content), strtolower($focus_keyword));
        $keyword_density = $word_count > 0 ? round($keyword_count / $word_count * 100, 2) : 0;
        
        return array(
            'analysis_available' => true,
            'keyword_count' => $keyword_count,
            'keyword_density' => $keyword_density,
            'density_optimal' => $keyword_density >= 1 && $keyword_density <= 3,
            'first_paragraph_keyword' => $this->check_first_paragraph_keyword($content, $focus_keyword),
            'last_paragraph_keyword' => $this->check_last_paragraph_keyword($content, $focus_keyword),
            'keyword_distribution_score' => $this->analyze_keyword_distribution($content, $focus_keyword)
        );
    }
    
    private function analyze_heading_structure($content) {
        $h1_count = preg_match_all('/<h1[^>]*>/i', $content);
        $h2_count = preg_match_all('/<h2[^>]*>/i', $content);
        $h3_count = preg_match_all('/<h3[^>]*>/i', $content);
        $h4_count = preg_match_all('/<h4[^>]*>/i', $content);
        
        preg_match_all('/<h([1-6])[^>]*>(.*?)<\/h[1-6]>/i', $content, $headings, PREG_SET_ORDER);
        
        return array(
            'h1_count' => $h1_count,
            'h2_count' => $h2_count,
            'h3_count' => $h3_count,
            'h4_count' => $h4_count,
            'total_headings' => count($headings),
            'heading_hierarchy_valid' => $this->validate_heading_hierarchy($headings),
            'average_content_per_heading' => $this->calculate_content_per_heading($content, count($headings)),
            'heading_keyword_optimization' => $this->analyze_heading_keyword_usage($headings, ''),
            'heading_structure_score' => $this->calculate_heading_structure_score($h1_count, $h2_count, $h3_count, count($headings))
        );
    }
    
    private function analyze_internal_links($content) {
        preg_match_all('/<a[^>]+href=["\']([^"\']*)["\'][^>]*>(.*?)<\/a>/i', $content, $links, PREG_SET_ORDER);
        
        $internal_links = 0;
        $external_links = 0;
        $current_domain = parse_url(home_url(), PHP_URL_HOST);
        
        foreach ($links as $link) {
            $url = $link[1];
            $link_domain = parse_url($url, PHP_URL_HOST);
            
            if (empty($link_domain) || $link_domain === $current_domain) {
                $internal_links++;
            } else {
                $external_links++;
            }
        }
        
        return array(
            'total_links' => count($links),
            'internal_links' => $internal_links,
            'external_links' => $external_links,
            'internal_link_ratio' => count($links) > 0 ? round($internal_links / count($links) * 100, 1) : 0,
            'recommended_internal_links' => max(2, floor(str_word_count(strip_tags($content)) / 300)),
            'internal_linking_score' => $this->calculate_internal_linking_score($internal_links, str_word_count(strip_tags($content)))
        );
    }
    
    private function analyze_external_links($content) {
        preg_match_all('/<a[^>]+href=["\']([^"\']*)["\'][^>]*>(.*?)<\/a>/i', $content, $links, PREG_SET_ORDER);
        
        $external_links = 0;
        $nofollow_links = 0;
        $current_domain = parse_url(home_url(), PHP_URL_HOST);
        
        foreach ($links as $link) {
            $url = $link[1];
            $link_domain = parse_url($url, PHP_URL_HOST);
            
            if (!empty($link_domain) && $link_domain !== $current_domain) {
                $external_links++;
                if (stripos($link[0], 'rel="nofollow"') !== false) {
                    $nofollow_links++;
                }
            }
        }
        
        return array(
            'external_links' => $external_links,
            'nofollow_links' => $nofollow_links,
            'follow_links' => $external_links - $nofollow_links,
            'nofollow_ratio' => $external_links > 0 ? round($nofollow_links / $external_links * 100, 1) : 0,
            'external_linking_score' => $this->calculate_external_linking_score($external_links, $nofollow_links)
        );
    }
    
    private function analyze_image_optimization($content) {
        preg_match_all('/<img[^>]*>/i', $content, $images);
        $image_count = count($images[0]);
        
        $images_with_alt = 0;
        $images_with_title = 0;
        $optimized_images = 0;
        
        foreach ($images[0] as $img) {
            if (preg_match('/alt=["\']([^"\']*)["\']/', $img, $alt_match)) {
                if (!empty(trim($alt_match[1]))) {
                    $images_with_alt++;
                }
            }
            
            if (preg_match('/title=["\']([^"\']*)["\']/', $img)) {
                $images_with_title++;
            }
            
            // Check for optimized file names (not just numbers)
            if (preg_match('/src=["\']([^"\']*)["\']/', $img, $src_match)) {
                $filename = basename($src_match[1]);
                if (!preg_match('/^\d+\.(jpg|jpeg|png|gif|webp)$/i', $filename)) {
                    $optimized_images++;
                }
            }
        }
        
        return array(
            'total_images' => $image_count,
            'images_with_alt' => $images_with_alt,
            'images_with_title' => $images_with_title,
            'optimized_filenames' => $optimized_images,
            'alt_text_ratio' => $image_count > 0 ? round($images_with_alt / $image_count * 100, 1) : 0,
            'image_optimization_score' => $this->calculate_image_optimization_score($image_count, $images_with_alt, $optimized_images)
        );
    }
    
    private function analyze_schema_markup($post_id) {
        $schema_markup = get_post_meta($post_id, '_quicky_schema_markup_enhanced', true);
        $content_type = get_post_meta($post_id, '_quicky_content_type', true);
        
        return array(
            'schema_present' => !empty($schema_markup),
            'content_type' => $content_type,
            'schema_type' => $this->determine_schema_type($content_type),
            'schema_completeness' => $this->analyze_schema_completeness($schema_markup, $content_type),
            'schema_validation' => $this->validate_schema_markup($schema_markup),
            'schema_score' => $this->calculate_schema_score($schema_markup, $content_type)
        );
    }
    
    private function analyze_url_structure($post_id) {
        $post_slug = get_post_field('post_name', $post_id);
        $focus_keyword = get_post_meta($post_id, '_yoast_wpseo_focuskw', true);
        
        return array(
            'slug' => $post_slug,
            'slug_length' => strlen($post_slug),
            'slug_word_count' => count(explode('-', $post_slug)),
            'keyword_in_slug' => !empty($focus_keyword) && stripos($post_slug, str_replace(' ', '-', $focus_keyword)) !== false,
            'slug_readability' => $this->analyze_slug_readability($post_slug),
            'url_score' => $this->calculate_url_score($post_slug, $focus_keyword)
        );
    }
    
    private function calculate_keyword_density($content, $focus_keyword) {
        if (empty($focus_keyword)) return 0;
        
        $clean_content = strip_tags($content);
        $word_count = str_word_count($clean_content);
        $keyword_count = substr_count(strtolower($clean_content), strtolower($focus_keyword));
        
        return $word_count > 0 ? round($keyword_count / $word_count * 100, 2) : 0;
    }
    
    private function find_semantic_keywords($content, $focus_keyword) {
        // Simulation de la recherche de mots-clés sémantiques
        // En production, cela utiliserait une API de mots-clés
        $semantic_keywords = array();
        
        if (stripos($focus_keyword, 'recipe') !== false) {
            $semantic_keywords = ['cooking', 'ingredients', 'instructions', 'kitchen', 'meal', 'food'];
        } elseif (stripos($focus_keyword, 'review') !== false) {
            $semantic_keywords = ['comparison', 'features', 'pros', 'cons', 'rating', 'quality'];
        } elseif (stripos($focus_keyword, 'guide') !== false) {
            $semantic_keywords = ['tips', 'advice', 'instructions', 'tutorial', 'step-by-step', 'beginner'];
        }
        
        $found_keywords = array();
        $clean_content = strtolower(strip_tags($content));
        
        foreach ($semantic_keywords as $keyword) {
            if (stripos($clean_content, $keyword) !== false) {
                $found_keywords[] = $keyword;
            }
        }
        
        return $found_keywords;
    }
    
    private function analyze_content_depth($content) {
        $word_count = str_word_count(strip_tags($content));
        $paragraph_count = substr_count($content, '</p>');
        $list_count = preg_match_all('/<(ul|ol)/', $content);
        $heading_count = preg_match_all('/<h[2-6]/', $content);
        
        $depth_score = 0;
        
        // Score basé sur la longueur
        if ($word_count >= 2000) $depth_score += 30;
        elseif ($word_count >= 1500) $depth_score += 25;
        elseif ($word_count >= 1000) $depth_score += 20;
        elseif ($word_count >= 500) $depth_score += 10;
        
        // Score basé sur la structure
        if ($heading_count >= 5) $depth_score += 20;
        elseif ($heading_count >= 3) $depth_score += 15;
        elseif ($heading_count >= 1) $depth_score += 10;
        
        // Score basé sur le contenu organisé
        if ($list_count >= 3) $depth_score += 15;
        elseif ($list_count >= 1) $depth_score += 10;
        
        // Score basé sur la densité d'information
        $avg_words_per_paragraph = $paragraph_count > 0 ? $word_count / $paragraph_count : 0;
        if ($avg_words_per_paragraph >= 80 && $avg_words_per_paragraph <= 150) $depth_score += 15;
        
        return array(
            'word_count' => $word_count,
            'paragraph_count' => $paragraph_count,
            'heading_count' => $heading_count,
            'list_count' => $list_count,
            'avg_words_per_paragraph' => round($avg_words_per_paragraph, 1),
            'depth_score' => min(100, $depth_score),
            'depth_level' => $this->get_depth_level($depth_score)
        );
    }
    
    private function detect_expertise_signals($content) {
        $expertise_indicators = array(
            'statistics' => preg_match_all('/\d+%|\d+\s*(percent|percentage)/', $content),
            'citations' => preg_match_all('/according to|research shows|study reveals|data indicates/', $content),
            'specific_numbers' => preg_match_all('/\d+\.\d+|\d{3,}/', $content),
            'technical_terms' => $this->count_technical_terms($content),
            'authoritative_sources' => $this->count_authoritative_sources($content),
            'case_studies' => preg_match_all('/case study|example|for instance|in practice/', $content),
            'professional_language' => $this->detect_professional_language($content)
        );
        
        $expertise_score = array_sum($expertise_indicators) * 2;
        
        return array_merge($expertise_indicators, array(
            'expertise_score' => min(100, $expertise_score),
            'expertise_level' => $this->get_expertise_level($expertise_score)
        ));
    }
    
    private function detect_trustworthiness_indicators($content) {
        $trust_signals = array(
            'transparency_indicators' => preg_match_all('/tested|verified|proven|certified|guarantee/', $content),
            'author_credentials' => preg_match_all('/expert|professional|certified|licensed|experienced/', $content),
            'contact_information' => preg_match_all('/contact|email|phone|address/', $content),
            'reviews_testimonials' => preg_match_all('/review|testimonial|feedback|rating|stars/', $content),
            'updated_information' => preg_match_all('/updated|current|latest|recent|2024|2025/', $content),
            'fact_checking' => preg_match_all('/source|reference|link|study|research/', $content),
            'transparency_language' => preg_match_all('/honest|transparent|unbiased|objective|disclosure/', $content)
        );
        
        $trust_score = array_sum($trust_signals) * 3;
        
        return array_merge($trust_signals, array(
            'trust_score' => min(100, $trust_score),
            'trust_level' => $this->get_trust_level($trust_score)
        ));
    }
    
    private function analyze_content_freshness($content) {
        $current_year = date('Y');
        $last_year = $current_year - 1;
        
        $freshness_indicators = array(
            'current_year_mentions' => substr_count($content, $current_year),
            'recent_year_mentions' => substr_count($content, $last_year),
            'freshness_words' => preg_match_all('/new|latest|recent|updated|current|modern|today/', $content),
            'outdated_references' => preg_match_all('/\b20(1[0-9]|20)\b/', $content) - substr_count($content, $current_year) - substr_count($content, $last_year),
            'trending_topics' => $this->detect_trending_topics($content)
        );
        
        $freshness_score = ($freshness_indicators['current_year_mentions'] * 10) + 
                          ($freshness_indicators['recent_year_mentions'] * 5) + 
                          ($freshness_indicators['freshness_words'] * 2) - 
                          ($freshness_indicators['outdated_references'] * 5) + 
                          ($freshness_indicators['trending_topics'] * 15);
        
        return array_merge($freshness_indicators, array(
            'freshness_score' => max(0, min(100, $freshness_score)),
            'freshness_level' => $this->get_freshness_level($freshness_score)
        ));
    }
    
    private function analyze_content_uniqueness($content) {
        // Simulation d'analyse d'unicité
        // En production, cela utiliserait une API de détection de plagiat
        
        $uniqueness_indicators = array(
            'original_insights' => preg_match_all('/in my experience|personally|our testing|we found/', $content),
            'unique_examples' => preg_match_all('/for example|specifically|in particular/', $content),
            'personal_anecdotes' => preg_match_all('/I remember|when I|my story|personal/', $content),
            'original_data' => preg_match_all('/our data|our research|we tested|we discovered/', $content),
            'unique_perspective' => preg_match_all('/unlike|different from|alternative|unique/', $content)
        );
        
        $uniqueness_score = array_sum($uniqueness_indicators) * 5;
        
        return array_merge($uniqueness_indicators, array(
            'uniqueness_score' => min(100, $uniqueness_score),
            'uniqueness_level' => $this->get_uniqueness_level($uniqueness_score)
        ));
    }
    
    private function predict_engagement_potential($content) {
        $engagement_factors = array(
            'questions_to_reader' => preg_match_all('/\?/', $content),
            'call_to_actions' => preg_match_all('/click|share|comment|subscribe|download|try/', $content),
            'emotional_words' => $this->count_emotional_words($content),
            'story_elements' => preg_match_all('/story|imagine|picture|remember|think about/', $content),
            'interactive_elements' => preg_match_all('/quiz|poll|survey|calculator|tool/', $content),
            'social_proof' => preg_match_all('/people|users|customers|reviews|testimonials/', $content),
            'urgency_words' => preg_match_all('/now|today|limited|hurry|quick|fast/', $content)
        );
        
        $engagement_score = array_sum($engagement_factors) * 3;
        
        return array_merge($engagement_factors, array(
            'engagement_score' => min(100, $engagement_score),
            'engagement_level' => $this->get_engagement_level($engagement_score)
        ));
    }
    
    private function analyze_emotional_impact($content) {
        $positive_emotions = preg_match_all('/amazing|awesome|fantastic|incredible|wonderful|excellent|perfect|love|happy|excited|thrilled/', $content);
        $negative_emotions = preg_match_all('/terrible|awful|horrible|disappointing|frustrated|angry|sad|worried|concerned/', $content);
        $neutral_emotions = preg_match_all('/okay|fine|average|normal|standard|typical|usual/', $content);
        
        $emotional_intensity = $positive_emotions + $negative_emotions;
        $emotional_balance = $positive_emotions - $negative_emotions;
        
        return array(
            'positive_emotions' => $positive_emotions,
            'negative_emotions' => $negative_emotions,
            'neutral_emotions' => $neutral_emotions,
            'emotional_intensity' => $emotional_intensity,
            'emotional_balance' => $emotional_balance,
            'emotional_score' => min(100, $emotional_intensity * 5 + max(0, $emotional_balance) * 2),
            'emotional_tone' => $this->determine_emotional_tone($positive_emotions, $negative_emotions, $neutral_emotions)
        );
    }
    
    private function analyze_actionability($content) {
        $action_indicators = array(
            'imperative_verbs' => preg_match_all('/\b(do|make|create|build|start|begin|try|use|apply|implement|follow|complete)\b/i', $content),
            'step_indicators' => preg_match_all('/step|first|second|third|next|then|finally/', $content),
            'instructional_language' => preg_match_all('/how to|guide|tutorial|instructions|method|process/', $content),
            'specific_actions' => preg_match_all('/click|download|install|setup|configure|adjust|measure/', $content),
            'tools_mentioned' => preg_match_all('/tool|software|app|calculator|template|checklist/', $content),
            'outcomes_described' => preg_match_all('/result|outcome|achieve|accomplish|complete|finish/', $content)
        );
        
        $actionability_score = array_sum($action_indicators) * 4;
        
        return array_merge($action_indicators, array(
            'actionability_score' => min(100, $actionability_score),
            'actionability_level' => $this->get_actionability_level($actionability_score)
        ));
    }
    
    private function analyze_comprehensiveness($content) {
        $comprehensiveness_factors = array(
            'topics_covered' => $this->count_distinct_topics($content),
            'detail_level' => $this->assess_detail_level($content),
            'examples_provided' => preg_match_all('/example|instance|case|sample|illustration/', $content),
            'alternatives_mentioned' => preg_match_all('/alternative|option|choice|instead|other/', $content),
            'edge_cases_covered' => preg_match_all('/however|although|except|unless|special case/', $content),
            'follow_up_resources' => preg_match_all('/more info|additional|further|related|see also/', $content)
        );
        
        $comprehensiveness_score = array_sum($comprehensiveness_factors) * 3;
        
        return array_merge($comprehensiveness_factors, array(
            'comprehensiveness_score' => min(100, $comprehensiveness_score),
            'comprehensiveness_level' => $this->get_comprehensiveness_level($comprehensiveness_score)
        ));
    }
    
    /**
     * FONCTIONS UTILITAIRES DE CALCUL
     */
    
    private function calculate_sentence_variety_score($short, $medium, $long, $very_long) {
        $total = $short + $medium + $long + $very_long;
        if ($total === 0) return 0;
        
        $ideal_distribution = array(0.3, 0.4, 0.25, 0.05);
        $actual_distribution = array($short/$total, $medium/$total, $long/$total, $very_long/$total);
        
        $variance = 0;
        for ($i = 0; $i < 4; $i++) {
            $variance += pow($ideal_distribution[$i] - $actual_distribution[$i], 2);
        }
        
        return max(0, round((1 - $variance) * 100, 1));
    }
    
    private function calculate_seo_score($seo_analysis) {
        $scores = array();
        
        if (isset($seo_analysis['title_analysis']['title_score'])) {
            $scores[] = $seo_analysis['title_analysis']['title_score'];
        }
        
        if (isset($seo_analysis['meta_description_analysis']['description_score'])) {
            $scores[] = $seo_analysis['meta_description_analysis']['description_score'];
        }
        
        if (isset($seo_analysis['content_seo']['analysis_available']) && $seo_analysis['content_seo']['analysis_available']) {
            $content_score = 50;
            if ($seo_analysis['content_seo']['density_optimal']) $content_score += 25;
            if ($seo_analysis['content_seo']['first_paragraph_keyword']) $content_score += 15;
            if ($seo_analysis['content_seo']['last_paragraph_keyword']) $content_score += 10;
            $scores[] = $content_score;
        }
        
        if (isset($seo_analysis['heading_structure']['heading_structure_score'])) {
            $scores[] = $seo_analysis['heading_structure']['heading_structure_score'];
        }
        
        if (isset($seo_analysis['internal_links']['internal_linking_score'])) {
            $scores[] = $seo_analysis['internal_links']['internal_linking_score'];
        }
        
        return count($scores) > 0 ? round(array_sum($scores) / count($scores), 1) : 0;
    }
    
    private function calculate_quality_score($quality_metrics) {
        $weights = array(
            'content_depth' => 0.25,
            'expertise_signals' => 0.20,
            'trustworthiness_indicators' => 0.15,
            'freshness_score' => 0.10,
            'uniqueness_score' => 0.15,
            'engagement_potential' => 0.10,
            'actionability_score' => 0.05
        );
        
        $weighted_score = 0;
        foreach ($weights as $metric => $weight) {
            if (isset($quality_metrics[$metric]['score']) || isset($quality_metrics[$metric])) {
                $score = isset($quality_metrics[$metric]['score']) ? $quality_metrics[$metric]['score'] : $quality_metrics[$metric];
                $weighted_score += $score * $weight;
            }
        }
        
        return round($weighted_score, 1);
    }
    
    private function calculate_structure_score($structure_analysis) {
        $score = 50; // Base score
        
        if (isset($structure_analysis['heading_hierarchy']) && $structure_analysis['heading_hierarchy']['heading_structure_score']) {
            $score = $structure_analysis['heading_hierarchy']['heading_structure_score'];
        }
        
        return $score;
    }
    
    /**
     * FONCTIONS DE NIVEAU/CLASSIFICATION
     */
    
    private function get_depth_level($score) {
        if ($score >= 80) return 'Comprehensive';
        if ($score >= 60) return 'Detailed';
        if ($score >= 40) return 'Moderate';
        return 'Basic';
    }
    
    private function get_expertise_level($score) {
        if ($score >= 80) return 'Expert';
        if ($score >= 60) return 'Advanced';
        if ($score >= 40) return 'Intermediate';
        return 'Beginner';
    }
    
    private function get_trust_level($score) {
        if ($score >= 80) return 'Highly Trustworthy';
        if ($score >= 60) return 'Trustworthy';
        if ($score >= 40) return 'Moderately Trustworthy';
        return 'Low Trust';
    }
    
    private function get_freshness_level($score) {
        if ($score >= 80) return 'Very Fresh';
        if ($score >= 60) return 'Fresh';
        if ($score >= 40) return 'Moderately Fresh';
        return 'Outdated';
    }
    
    private function get_uniqueness_level($score) {
        if ($score >= 80) return 'Highly Unique';
        if ($score >= 60) return 'Unique';
        if ($score >= 40) return 'Somewhat Unique';
        return 'Common';
    }
    
    private function get_engagement_level($score) {
        if ($score >= 80) return 'Highly Engaging';
        if ($score >= 60) return 'Engaging';
        if ($score >= 40) return 'Moderately Engaging';
        return 'Low Engagement';
    }
    
    private function get_actionability_level($score) {
        if ($score >= 80) return 'Highly Actionable';
        if ($score >= 60) return 'Actionable';
        if ($score >= 40) return 'Somewhat Actionable';
        return 'Not Actionable';
    }
    
    private function get_comprehensiveness_level($score) {
        if ($score >= 80) return 'Comprehensive';
        if ($score >= 60) return 'Thorough';
        if ($score >= 40) return 'Adequate';
        return 'Incomplete';
    }
    
    /**
     * FONCTIONS D'ANALYSE RAPIDE POUR TEMPS RÉEL
     */
    
    private function quick_readability_check($content) {
        $words = str_word_count(strip_tags($content));
        $sentences = preg_match_all('/[.!?]+/', strip_tags($content));
        
        if ($sentences === 0) return 50;
        
        $avg_words_per_sentence = $words / $sentences;
        
        if ($avg_words_per_sentence <= 15) return 90;
        if ($avg_words_per_sentence <= 20) return 75;
        if ($avg_words_per_sentence <= 25) return 60;
        if ($avg_words_per_sentence <= 30) return 45;
        return 30;
    }
    
    private function quick_seo_check($post_id, $content) {
        $score = 50;
        
        $title = get_the_title($post_id);
        $title_length = strlen($title);
        if ($title_length >= 50 && $title_length <= 60) $score += 20;
        
        $h2_count = preg_match_all('/<h2/', $content);
        if ($h2_count >= 2) $score += 15;
        
        $word_count = str_word_count(strip_tags($content));
        if ($word_count >= 1000) $score += 15;
        
        return min(100, $score);
    }
    
    private function quick_structure_check($content) {
        $score = 40;
        
        $paragraphs = substr_count($content, '</p>');
        $headings = preg_match_all('/<h[2-6]/', $content);
        $lists = preg_match_all('/<(ul|ol)/', $content);
        
        if ($headings >= 3) $score += 20;
        if ($lists >= 1) $score += 20;
        if ($paragraphs >= 5) $score += 20;
        
        return min(100, $score);
    }
    
    private function get_quick_suggestions($content) {
        $suggestions = array();
        $word_count = str_word_count(strip_tags($content));
        
        if ($word_count < 300) {
            $suggestions[] = 'Add more content (aim for 1000+ words)';
        }
        
        $headings = preg_match_all('/<h[2-6]/', $content);
        if ($headings < 2) {
            $suggestions[] = 'Add more subheadings (H2, H3)';
        }
        
        $sentences = preg_match_all('/[.!?]+/', strip_tags($content));
        if ($sentences > 0) {
            $avg_words = $word_count / $sentences;
            if ($avg_words > 25) {
                $suggestions[] = 'Use shorter sentences for better readability';
            }
        }
        
        $lists = preg_match_all('/<(ul|ol)/', $content);
        if ($lists === 0) {
            $suggestions[] = 'Add bullet points or numbered lists';
        }
        
        return $suggestions;
    }
    
    /**
     * MÉTHODES PLACEHOLDER POUR FONCTIONNALITÉS AVANCÉES
     */
    
    private function count_technical_terms($content) {
        // Placeholder - en production, utiliserait un dictionnaire de termes techniques
        return substr_count(strtolower($content), 'technical') + 
               substr_count(strtolower($content), 'advanced') + 
               substr_count(strtolower($content), 'professional');
    }
    
    private function count_authoritative_sources($content) {
        return preg_match_all('/\.edu|\.gov|research|study|university|institute/', $content);
    }
    
    private function detect_professional_language($content) {
        $professional_terms = ['methodology', 'analysis', 'evaluation', 'assessment', 'implementation', 'optimization'];
        $count = 0;
        foreach ($professional_terms as $term) {
            $count += substr_count(strtolower($content), $term);
        }
        return $count;
    }
    
    private function detect_trending_topics($content) {
        $trending_terms = ['ai', 'artificial intelligence', 'machine learning', 'automation', 'digital', 'smart'];
        $count = 0;
        foreach ($trending_terms as $term) {
            $count += substr_count(strtolower($content), $term);
        }
        return $count;
    }
    
    private function count_emotional_words($content) {
        $emotional_words = ['amazing', 'incredible', 'fantastic', 'wonderful', 'excellent', 'perfect', 'love', 'hate', 'excited', 'disappointed'];
        $count = 0;
        foreach ($emotional_words as $word) {
            $count += substr_count(strtolower($content), $word);
        }
        return $count;
    }
    
    private function determine_emotional_tone($positive, $negative, $neutral) {
        if ($positive > $negative && $positive > $neutral) return 'Positive';
        if ($negative > $positive && $negative > $neutral) return 'Negative';
        if ($neutral > $positive && $neutral > $negative) return 'Neutral';
        return 'Mixed';
    }
    
    private function count_distinct_topics($content) {
        // Placeholder - en production, utiliserait NLP pour identifier les sujets
        $headings = preg_match_all('/<h[2-6][^>]*>(.*?)<\/h[2-6]>/i', $content);
        return max(1, $headings);
    }
    
    private function assess_detail_level($content) {
        $word_count = str_word_count(strip_tags($content));
        if ($word_count >= 2000) return 5;
        if ($word_count >= 1500) return 4;
        if ($word_count >= 1000) return 3;
        if ($word_count >= 500) return 2;
        return 1;
    }
    
    // Méthodes placeholder pour les scores de calcul spécifiques
    private function calculate_title_score($length, $keyword_present, $keyword_position) {
        $score = 50;
        if ($length >= 50 && $length <= 60) $score += 30;
        if ($keyword_present) $score += 20;
        return min(100, $score);
    }
    
    private function calculate_description_score($length, $keyword_present) {
        $score = 50;
        if ($length >= 150 && $length <= 160) $score += 30;
        if ($keyword_present) $score += 20;
        return min(100, $score);
    }
    
    private function detect_call_to_action($text) {
        return preg_match('/learn more|read more|click here|download|subscribe|try|get|start/', strtolower($text)) > 0;
    }
    
    private function check_first_paragraph_keyword($content, $keyword) {
        if (empty($keyword)) return false;
        preg_match('/<p[^>]*>(.*?)<\/p>/s', $content, $matches);
        return isset($matches[1]) && stripos($matches[1], $keyword) !== false;
    }
    
    private function check_last_paragraph_keyword($content, $keyword) {
        if (empty($keyword)) return false;
        preg_match_all('/<p[^>]*>(.*?)<\/p>/s', $content, $matches);
        $last_paragraph = end($matches[1]);
        return $last_paragraph && stripos($last_paragraph, $keyword) !== false;
    }
    
    // Placeholder methods pour les autres calculs
    private function validate_heading_hierarchy($headings) { return true; }
    private function calculate_content_per_heading($content, $heading_count) { return 200; }
    private function analyze_heading_keyword_usage($headings, $keyword) { return []; }
    private function calculate_heading_structure_score($h1, $h2, $h3, $total) { return 75; }
    private function calculate_internal_linking_score($internal_links, $word_count) { return 70; }
    private function calculate_external_linking_score($external_links, $nofollow_links) { return 75; }
    private function calculate_image_optimization_score($total, $with_alt, $optimized) { return 80; }
    private function determine_schema_type($content_type) { return 'Article'; }
    private function analyze_schema_completeness($schema, $content_type) { return 85; }
    private function validate_schema_markup($schema) { return true; }
    private function calculate_schema_score($schema, $content_type) { return 90; }
    private function analyze_slug_readability($slug) { return 80; }
    private function calculate_url_score($slug, $keyword) { return 75; }
    
    // Placeholder methods pour l'analyse concurrentielle
    private function simulate_content_gaps($keyword) { return ['More examples needed', 'Add case studies']; }
    private function assess_competitive_strength($post_id) { return 'Medium'; }
    private function identify_competitive_opportunities($keyword) { return ['Long-tail keywords', 'Video content']; }
    private function identify_competitive_threats($keyword) { return ['High competition', 'Established players']; }
    private function get_competitive_improvement_suggestions($post_id) { return ['Add more depth', 'Improve formatting']; }
    
    // Placeholder methods pour l'analyse détaillée
    private function analyze_heading_hierarchy($content) { return ['score' => 75]; }
    private function analyze_paragraph_structure($content) { return ['avg_length' => 100]; }
    private function analyze_list_usage($content) { return ['count' => 3]; }
    private function analyze_visual_elements($content) { return ['images' => 5]; }
    private function analyze_content_flow($content) { return ['score' => 80]; }
    private function analyze_cta_elements($content) { return ['count' => 2]; }
    private function analyze_table_usage($content) { return ['count' => 1]; }
    private function analyze_code_blocks($content) { return ['count' => 0]; }
    private function analyze_quote_usage($content) { return ['count' => 1]; }
    
    private function check_keyword_in_title($title, $keyword) { return stripos($title, $keyword) !== false; }
    private function analyze_keyword_in_content($content, $keyword) { return ['present' => true]; }
    private function analyze_keyword_distribution($content, $keyword) { return ['even' => true]; }
    private function find_long_tail_variations($content, $keyword) { return []; }
    private function calculate_keyword_prominence($content, $keyword) { return 75; }
    private function find_related_keywords($content, $keyword) { return []; }
    private function check_keyword_stuffing($content, $keyword) { return false; }
    private function find_lsi_keywords($content, $keyword) { return []; }
    
    private function get_specific_suggestions($post_id, $type) { return ['suggestion1', 'suggestion2']; }
    private function perform_competitor_analysis($keyword, $post_id) { return ['competitors' => 5]; }
    
    // Méthodes d'identification des forces et faiblesses
    private function identify_content_strengths($analysis) {
        $strengths = [];
        
        if ($analysis['readability']['flesch_score'] >= 70) {
            $strengths[] = 'Excellent readability score';
        }
        
        if ($analysis['seo_optimization']['overall_seo_score'] >= 80) {
            $strengths[] = 'Well optimized for SEO';
        }
        
        if ($analysis['content_quality']['overall_quality_score'] >= 80) {
            $strengths[] = 'High quality content';
        }
        
        return $strengths;
    }
    
    private function identify_content_weaknesses($analysis) {
        $weaknesses = [];
        
        if ($analysis['readability']['flesch_score'] < 50) {
            $weaknesses[] = 'Poor readability - too complex';
        }
        
        if ($analysis['seo_optimization']['overall_seo_score'] < 60) {
            $weaknesses[] = 'SEO optimization needs improvement';
        }
        
        if ($analysis['content_quality']['overall_quality_score'] < 60) {
            $weaknesses[] = 'Content quality below standards';
        }
        
        return $weaknesses;
    }
    
    private function generate_action_items($analysis) {
        $actions = [];
        
        if ($analysis['overall_score'] < 70) {
            $actions[] = [
                'priority' => 'high',
                'action' => 'Improve overall content quality',
                'deadline' => 'This week'
            ];
        }
        
        if ($analysis['readability']['flesch_score'] < 60) {
            $actions[] = [
                'priority' => 'medium',
                'action' => 'Simplify language and shorten sentences',
                'deadline' => 'Next week'
            ];
        }
        
        return $actions;
    }
}

// Initialisation
new QuickyContentAnalyzer();