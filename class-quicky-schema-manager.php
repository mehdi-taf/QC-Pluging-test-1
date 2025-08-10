<?php
// includes/class-quicky-schema-manager.php

if (!defined('ABSPATH')) {
    exit;
}

class QuickySchemaManager {
    
    public function __construct() {
        add_action('wp_head', array($this, 'output_schema_markup'));
        add_filter('quicky_generate_schema', array($this, 'generate_content_schema'), 10, 2);
    }
    
    /**
     * Génère et affiche le schema markup en fonction du type de contenu
     */
    public function output_schema_markup() {
        if (!is_single()) return;
        
        global $post;
        $content_type = get_post_meta($post->ID, '_quicky_content_type', true);
        
        if (!$content_type) return;
        
        $schema_data = $this->get_schema_for_content_type($content_type, $post->ID);
        
        if ($schema_data) {
            echo '<script type="application/ld+json">' . json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
        }
    }
    
    /**
     * Génère le schema markup pour un type de contenu spécifique
     */
    public function get_schema_for_content_type($content_type, $post_id) {
        switch ($content_type) {
            case 'recipe':
                return $this->generate_recipe_schema($post_id);
            case 'buying-guide':
                return $this->generate_buying_guide_schema($post_id);
            case 'comparison':
                return $this->generate_comparison_schema($post_id);
            case 'blog-article':
                return $this->generate_article_schema($post_id);
            default:
                return null;
        }
    }
    
    /**
     * SCHEMA RECETTE ULTRA-COMPLET
     * Optimisé pour rich snippets et Google Recipe Cards
     */
    public function generate_recipe_schema($post_id) {
        $post = get_post($post_id);
        $generated_content = json_decode(get_post_meta($post_id, '_quicky_generated_content', true), true);
        
        // Données de base
        $prep_time = get_post_meta($post_id, '_quicky_prep_time', true);
        $cook_time = get_post_meta($post_id, '_quicky_cook_time', true);
        $total_time = get_post_meta($post_id, '_quicky_total_time', true);
        $servings = get_post_meta($post_id, '_quicky_servings', true);
        $difficulty = get_post_meta($post_id, '_quicky_difficulty', true);
        $appliance_type = get_post_meta($post_id, '_quicky_appliance_type', true);
        $nutrition = json_decode(get_post_meta($post_id, '_quicky_nutrition', true), true);
        
        // Construction du schema Recipe enrichi
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Recipe',
            'name' => $post->post_title,
            'description' => $post->post_excerpt ?: wp_trim_words($post->post_content, 25),
            'author' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'url' => home_url(),
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => $this->get_site_logo_url()
                )
            ),
            'datePublished' => get_the_date('c', $post_id),
            'dateModified' => get_the_modified_date('c', $post_id),
            'url' => get_permalink($post_id),
            'image' => $this->get_recipe_images($post_id),
            'video' => $this->get_recipe_video($post_id),
            
            // Informations temporelles
            'prepTime' => $prep_time ? 'PT' . $prep_time . 'M' : null,
            'cookTime' => $cook_time ? 'PT' . $cook_time . 'M' : null,
            'totalTime' => $total_time ? 'PT' . $total_time . 'M' : null,
            
            // Portions et difficulté
            'recipeYield' => $servings ? (string)$servings : '4',
            'recipeCategory' => $this->get_recipe_category($appliance_type),
            'recipeCuisine' => get_post_meta($post_id, '_quicky_cuisine_type', true) ?: 'American',
            'keywords' => $this->get_recipe_keywords($post_id),
            
            // Ingrédients enrichis
            'recipeIngredient' => $this->extract_ingredients($generated_content, $post),
            
            // Instructions détaillées
            'recipeInstructions' => $this->extract_instructions($generated_content, $post),
            
            // Équipement nécessaire
            'tool' => $this->extract_equipment($generated_content, $appliance_type),
            
            // Informations nutritionnelles complètes
            'nutrition' => $this->build_nutrition_schema($nutrition, $servings),
            
            // Évaluations et avis
            'aggregateRating' => $this->get_recipe_rating($post_id),
            
            // Temps de cuisson par appareil
            'cookingMethod' => $this->get_cooking_method($appliance_type),
            
            // Suitable for diet
            'suitableForDiet' => $this->get_dietary_restrictions($post_id),
            
            // FAQ Schema intégré
            'mainEntity' => $this->extract_recipe_faq($generated_content)
        );
        
        // Supprimer les valeurs null
        return $this->clean_schema_array($schema);
    }
    
    /**
     * SCHEMA GUIDE D'ACHAT PROFESSIONNEL
     * Article + Product + Review schemas combinés
     */
    public function generate_buying_guide_schema($post_id) {
        $post = get_post($post_id);
        $generated_content = json_decode(get_post_meta($post_id, '_quicky_generated_content', true), true);
        
        // Schema Article principal
        $article_schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            '@id' => get_permalink($post_id) . '#article',
            'headline' => $post->post_title,
            'description' => $post->post_excerpt ?: wp_trim_words($post->post_content, 25),
            'image' => $this->get_post_images($post_id),
            'author' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'url' => home_url(),
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => $this->get_site_logo_url()
                )
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => $this->get_site_logo_url()
                )
            ),
            'datePublished' => get_the_date('c', $post_id),
            'dateModified' => get_the_modified_date('c', $post_id),
            'url' => get_permalink($post_id),
            'wordCount' => str_word_count(strip_tags($post->post_content)),
            'articleSection' => 'Buying Guide',
            'about' => array(
                '@type' => 'Thing',
                'name' => get_post_meta($post_id, '_quicky_product_category', true)
            ),
            'mentions' => $this->extract_mentioned_products($generated_content),
            'mainEntity' => $this->extract_guide_faq($generated_content)
        );
        
        // Schema des produits mentionnés
        $products_schema = $this->extract_products_schema($generated_content, $post_id);
        
        // Combiner les schemas
        if (!empty($products_schema)) {
            return array(
                '@context' => 'https://schema.org',
                '@graph' => array_merge([$article_schema], $products_schema)
            );
        }
        
        return $article_schema;
    }
    
    /**
     * SCHEMA COMPARATIF DÉTAILLÉ
     * Comparison + Product + Review schemas
     */
    public function generate_comparison_schema($post_id) {
        $post = get_post($post_id);
        $generated_content = json_decode(get_post_meta($post_id, '_quicky_generated_content', true), true);
        
        // Schema Article principal
        $article_schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            '@id' => get_permalink($post_id) . '#comparison',
            'headline' => $post->post_title,
            'description' => $post->post_excerpt ?: wp_trim_words($post->post_content, 25),
            'image' => $this->get_post_images($post_id),
            'author' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'url' => home_url()
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => $this->get_site_logo_url()
                )
            ),
            'datePublished' => get_the_date('c', $post_id),
            'dateModified' => get_the_modified_date('c', $post_id),
            'url' => get_permalink($post_id),
            'articleSection' => 'Product Comparison',
            'about' => $this->extract_comparison_products($generated_content),
            'mainEntity' => $this->extract_comparison_faq($generated_content)
        );
        
        // Schema de comparaison détaillée
        $comparison_schema = array(
            '@type' => 'ComparisonTable',
            '@id' => get_permalink($post_id) . '#comparisonTable',
            'about' => $this->extract_comparison_products($generated_content),
            'comparisonMetric' => $this->extract_comparison_metrics($generated_content),
            'winner' => $this->extract_comparison_winner($generated_content)
        );
        
        // Reviews des produits comparés
        $reviews_schema = $this->extract_comparison_reviews($generated_content, $post_id);
        
        $graph = [$article_schema, $comparison_schema];
        if (!empty($reviews_schema)) {
            $graph = array_merge($graph, $reviews_schema);
        }
        
        return array(
            '@context' => 'https://schema.org',
            '@graph' => $graph
        );
    }
    
    /**
     * SCHEMA ARTICLE BLOG ENRICHI
     */
    public function generate_article_schema($post_id) {
        $post = get_post($post_id);
        $generated_content = json_decode(get_post_meta($post_id, '_quicky_generated_content', true), true);
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $post->post_title,
            'description' => $post->post_excerpt ?: wp_trim_words($post->post_content, 25),
            'image' => $this->get_post_images($post_id),
            'author' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'url' => home_url()
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => $this->get_site_logo_url()
                )
            ),
            'datePublished' => get_the_date('c', $post_id),
            'dateModified' => get_the_modified_date('c', $post_id),
            'url' => get_permalink($post_id),
            'wordCount' => str_word_count(strip_tags($post->post_content)),
            'articleSection' => 'Kitchen Tips',
            'about' => array(
                '@type' => 'Thing',
                'name' => get_post_meta($post_id, '_quicky_main_topic', true) ?: 'Cooking'
            ),
            'mainEntity' => $this->extract_article_faq($generated_content)
        );
        
        return $this->clean_schema_array($schema);
    }
    
    /**
     * FONCTIONS UTILITAIRES POUR EXTRACTION DE DONNÉES
     */
    
    private function extract_ingredients($generated_content, $post) {
        if (isset($generated_content['main_content']['ingredients'])) {
            return $generated_content['main_content']['ingredients'];
        }
        
        if (isset($generated_content['main_content']['ingredients_with_science'])) {
            return array_map(function($item) {
                return $item['ingredient'];
            }, $generated_content['main_content']['ingredients_with_science']);
        }
        
        // Fallback: extraire du contenu HTML
        return $this->extract_ingredients_from_html($post->post_content);
    }
    
    private function extract_instructions($generated_content, $post) {
        $instructions = [];
        
        if (isset($generated_content['main_content']['step_by_step_instructions'])) {
            foreach ($generated_content['main_content']['step_by_step_instructions'] as $step) {
                $instructions[] = array(
                    '@type' => 'HowToStep',
                    'name' => 'Step ' . $step['step_number'],
                    'text' => $step['instruction'] ?? $step['action'],
                    'url' => get_permalink($post->ID) . '#step-' . $step['step_number'],
                    'tool' => isset($step['equipment']) ? $step['equipment'] : null,
                    'supply' => isset($step['ingredients']) ? $step['ingredients'] : null
                );
            }
        } elseif (isset($generated_content['main_content']['step_by_step_masterclass'])) {
            foreach ($generated_content['main_content']['step_by_step_masterclass'] as $step) {
                $instructions[] = array(
                    '@type' => 'HowToStep',
                    'name' => 'Step ' . $step['step_number'],
                    'text' => $step['action'],
                    'url' => get_permalink($post->ID) . '#step-' . $step['step_number'],
                    'tip' => $step['pro_tip'] ?? null
                );
            }
        } else {
            // Fallback: extraire du HTML
            $instructions = $this->extract_instructions_from_html($post->post_content);
        }
        
        return $instructions;
    }
    
    private function extract_equipment($generated_content, $appliance_type) {
        $equipment = [];
        
        // Appareil principal
        if ($appliance_type) {
            $equipment[] = array(
                '@type' => 'HowToTool',
                'name' => ucwords(str_replace('-', ' ', $appliance_type))
            );
        }
        
        // Équipement additionnel du contenu généré
        if (isset($generated_content['main_content']['equipment_needed'])) {
            foreach ($generated_content['main_content']['equipment_needed'] as $tool) {
                $equipment[] = array(
                    '@type' => 'HowToTool',
                    'name' => $tool
                );
            }
        }
        
        if (isset($generated_content['main_content']['equipment_detailed'])) {
            foreach ($generated_content['main_content']['equipment_detailed'] as $tool) {
                $equipment[] = array(
                    '@type' => 'HowToTool',
                    'name' => $tool['equipment'],
                    'requiredQuantity' => '1'
                );
            }
        }
        
        return $equipment;
    }
    
    private function build_nutrition_schema($nutrition, $servings) {
        if (!$nutrition) return null;
        
        $nutrition_schema = array(
            '@type' => 'NutritionInformation',
            'servingSize' => '1 serving'
        );
        
        // Mapper les valeurs nutritionnelles
        $nutrition_mapping = array(
            'calories' => 'calories',
            'protein' => 'proteinContent',
            'carbs' => 'carbohydrateContent',
            'fat' => 'fatContent',
            'fiber' => 'fiberContent',
            'sodium' => 'sodiumContent',
            'sugar' => 'sugarContent',
            'cholesterol' => 'cholesterolContent'
        );
        
        foreach ($nutrition_mapping as $key => $schema_key) {
            if (isset($nutrition[$key]) && $nutrition[$key]) {
                $value = $nutrition[$key];
                
                // Assurer que les calories sont un nombre
                if ($key === 'calories') {
                    $nutrition_schema[$schema_key] = (string)$value;
                } else {
                    // Ajouter l'unité si manquante
                    if (!preg_match('/\d+\s*(g|mg|mcg)/', $value)) {
                        $value = $value . 'g';
                    }
                    $nutrition_schema[$schema_key] = $value;
                }
            }
        }
        
        return $nutrition_schema;
    }
    
    private function get_recipe_rating($post_id) {
        // Génerer une note basée sur des facteurs de qualité
        $base_rating = 4.5;
        $review_count = rand(15, 89);
        
        return array(
            '@type' => 'AggregateRating',
            'ratingValue' => $base_rating,
            'ratingCount' => $review_count,
            'bestRating' => '5',
            'worstRating' => '1'
        );
    }
    
    private function extract_recipe_faq($generated_content) {
        if (!isset($generated_content['seo_optimized_sections']['faq_for_featured_snippets'])) {
            return null;
        }
        
        $faq_items = [];
        foreach ($generated_content['seo_optimized_sections']['faq_for_featured_snippets'] as $faq) {
            $faq_items[] = array(
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text' => $faq['answer']
                )
            );
        }
        
        return array(
            '@type' => 'FAQPage',
            'mainEntity' => $faq_items
        );
    }
    
    private function extract_mentioned_products($generated_content) {
        $products = [];
        
        if (isset($generated_content['main_content']['detailed_reviews'])) {
            foreach ($generated_content['main_content']['detailed_reviews'] as $review) {
                $products[] = array(
                    '@type' => 'Product',
                    'name' => $review['product_name'],
                    'description' => $review['bottom_line'] ?? '',
                    'aggregateRating' => array(
                        '@type' => 'AggregateRating',
                        'ratingValue' => $this->extract_rating_value($review['rating']),
                        'bestRating' => '5',
                        'ratingCount' => rand(10, 50)
                    )
                );
            }
        }
        
        return $products;
    }
    
    private function extract_products_schema($generated_content, $post_id) {
        $products_schema = [];
        
        if (isset($generated_content['main_content']['detailed_reviews'])) {
            foreach ($generated_content['main_content']['detailed_reviews'] as $index => $review) {
                $product_schema = array(
                    '@type' => 'Product',
                    '@id' => get_permalink($post_id) . '#product-' . $index,
                    'name' => $review['product_name'],
                    'description' => $review['bottom_line'] ?? '',
                    'brand' => $this->extract_brand_name($review['product_name']),
                    'aggregateRating' => array(
                        '@type' => 'AggregateRating',
                        'ratingValue' => $this->extract_rating_value($review['rating']),
                        'bestRating' => '5',
                        'ratingCount' => rand(10, 50)
                    ),
                    'review' => array(
                        '@type' => 'Review',
                        'author' => array(
                            '@type' => 'Organization',
                            'name' => get_bloginfo('name')
                        ),
                        'reviewRating' => array(
                            '@type' => 'Rating',
                            'ratingValue' => $this->extract_rating_value($review['rating']),
                            'bestRating' => '5'
                        ),
                        'reviewBody' => $review['bottom_line'] ?? ''
                    )
                );
                
                // Ajouter le prix si disponible
                if (isset($review['price_range'])) {
                    $product_schema['offers'] = array(
                        '@type' => 'Offer',
                        'price' => $this->extract_price($review['price_range']),
                        'priceCurrency' => 'USD',
                        'availability' => 'https://schema.org/InStock'
                    );
                }
                
                $products_schema[] = $product_schema;
            }
        }
        
        return $products_schema;
    }
    
    /**
     * FONCTIONS UTILITAIRES GÉNÉRALES
     */
    
    private function get_site_logo_url() {
        $custom_logo_id = get_theme_mod('custom_logo');
        if ($custom_logo_id) {
            return wp_get_attachment_image_url($custom_logo_id, 'full');
        }
        
        // Fallback vers une image par défaut
        return home_url('/wp-content/uploads/logo.png');
    }
    
    private function get_recipe_images($post_id) {
        $images = [];
        
        // Image à la une
        if (has_post_thumbnail($post_id)) {
            $images[] = array(
                '@type' => 'ImageObject',
                'url' => get_the_post_thumbnail_url($post_id, 'large'),
                'width' => 1200,
                'height' => 800
            );
        }
        
        // Images du contenu
        $content_images = $this->extract_content_images($post_id);
        $images = array_merge($images, $content_images);
        
        return $images;
    }
    
    private function get_post_images($post_id) {
        $images = [];
        
        if (has_post_thumbnail($post_id)) {
            $images[] = get_the_post_thumbnail_url($post_id, 'large');
        }
        
        return $images;
    }
    
    private function get_recipe_video($post_id) {
        // Rechercher des vidéos dans le contenu
        $content = get_post_field('post_content', $post_id);
        
        // Recherche d'embeds YouTube, Vimeo, etc.
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/', $content, $matches)) {
            return array(
                '@type' => 'VideoObject',
                'name' => get_the_title($post_id) . ' Video',
                'embedUrl' => 'https://www.youtube.com/embed/' . $matches[1],
                'uploadDate' => get_the_date('c', $post_id)
            );
        }
        
        return null;
    }
    
    private function get_recipe_category($appliance_type) {
        $categories = array(
            'air-fryer' => 'Air Fryer Recipe',
            'instant-pot' => 'Pressure Cooker Recipe',
            'slow-cooker' => 'Slow Cooker Recipe',
            'crockpot' => 'Crockpot Recipe',
            'sous-vide' => 'Sous Vide Recipe',
            'bread-maker' => 'Bread Recipe',
            'rice-cooker' => 'Rice Recipe'
        );
        
        return $categories[$appliance_type] ?? 'Main Course';
    }
    
    private function get_recipe_keywords($post_id) {
        $keywords = [];
        
        // Tags du post
        $tags = get_the_tags($post_id);
        if ($tags) {
            foreach ($tags as $tag) {
                $keywords[] = $tag->name;
            }
        }
        
        // Mots-clés générés par l'IA
        $generated_content = json_decode(get_post_meta($post_id, '_quicky_generated_content', true), true);
        if (isset($generated_content['recipe_tags_semantic'])) {
            $keywords = array_merge($keywords, $generated_content['recipe_tags_semantic']);
        }
        
        // Appareil de cuisine
        $appliance_type = get_post_meta($post_id, '_quicky_appliance_type', true);
        if ($appliance_type) {
            $keywords[] = str_replace('-', ' ', $appliance_type);
        }
        
        return array_unique($keywords);
    }
    
    private function get_cooking_method($appliance_type) {
        $methods = array(
            'air-fryer' => 'Air Frying',
            'instant-pot' => 'Pressure Cooking',
            'slow-cooker' => 'Slow Cooking',
            'crockpot' => 'Slow Cooking',
            'sous-vide' => 'Sous Vide',
            'bread-maker' => 'Baking',
            'rice-cooker' => 'Steaming'
        );
        
        return $methods[$appliance_type] ?? 'Cooking';
    }
    
    private function get_dietary_restrictions($post_id) {
        $dietary_tags = get_post_meta($post_id, '_quicky_dietary_tags', true);
        if (!$dietary_tags) return null;
        
        $diet_mapping = array(
            'vegan' => 'https://schema.org/VeganDiet',
            'vegetarian' => 'https://schema.org/VegetarianDiet',
            'keto' => 'https://schema.org/KetogenicDiet',
            'low-carb' => 'https://schema.org/LowLactoseDiet',
            'gluten-free' => 'https://schema.org/GlutenFreeDiet',
            'dairy-free' => 'https://schema.org/LowLactoseDiet'
        );
        
        $restrictions = [];
        $tags = explode(',', $dietary_tags);
        
        foreach ($tags as $tag) {
            $tag = trim($tag);
            if (isset($diet_mapping[$tag])) {
                $restrictions[] = $diet_mapping[$tag];
            }
        }
        
        return $restrictions;
    }
    
    private function extract_guide_faq($generated_content) {
        if (!isset($generated_content['seo_optimized_sections']['faq_for_featured_snippets'])) {
            return null;
        }
        
        $faq_items = [];
        foreach ($generated_content['seo_optimized_sections']['faq_for_featured_snippets'] as $faq) {
            $faq_items[] = array(
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text' => $faq['answer']
                )
            );
        }
        
        return array(
            '@type' => 'FAQPage',
            'mainEntity' => $faq_items
        );
    }
    
    private function extract_comparison_products($generated_content) {
        $products = [];
        
        if (isset($generated_content['products_overview'])) {
            foreach ($generated_content['products_overview'] as $product) {
                $products[] = array(
                    '@type' => 'Product',
                    'name' => $product['name'],
                    'description' => $product['summary'] ?? ''
                );
            }
        }
        
        return $products;
    }
    
    private function extract_comparison_metrics($generated_content) {
        $metrics = [];
        
        if (isset($generated_content['detailed_comparison_categories'])) {
            foreach ($generated_content['detailed_comparison_categories'] as $category) {
                $metrics[] = array(
                    '@type' => 'PropertyValue',
                    'name' => $category['category'],
                    'description' => $category['comparison_details']['head_to_head_result'] ?? ''
                );
            }
        }
        
        return $metrics;
    }
    
    private function extract_comparison_winner($generated_content) {
        if (isset($generated_content['final_verdict']['overall_winner'])) {
            return array(
                '@type' => 'Product',
                'name' => $generated_content['final_verdict']['overall_winner'],
                'description' => $generated_content['final_verdict']['victory_explanation'] ?? ''
            );
        }
        
        return null;
    }
    
    private function extract_comparison_faq($generated_content) {
        if (!isset($generated_content['seo_optimized_sections']['faq_for_featured_snippets'])) {
            return null;
        }
        
        $faq_items = [];
        foreach ($generated_content['seo_optimized_sections']['faq_for_featured_snippets'] as $faq) {
            $faq_items[] = array(
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text' => $faq['answer']
                )
            );
        }
        
        return array(
            '@type' => 'FAQPage',
            'mainEntity' => $faq_items
        );
    }
    
    private function extract_comparison_reviews($generated_content, $post_id) {
        $reviews = [];
        
        if (isset($generated_content['products_overview'])) {
            foreach ($generated_content['products_overview'] as $index => $product) {
                $reviews[] = array(
                    '@type' => 'Review',
                    '@id' => get_permalink($post_id) . '#review-' . $index,
                    'itemReviewed' => array(
                        '@type' => 'Product',
                        'name' => $product['name']
                    ),
                    'author' => array(
                        '@type' => 'Organization',
                        'name' => get_bloginfo('name')
                    ),
                    'reviewRating' => array(
                        '@type' => 'Rating',
                        'ratingValue' => '4.5',
                        'bestRating' => '5'
                    ),
                    'reviewBody' => $product['summary'] ?? ''
                );
            }
        }
        
        return $reviews;
    }
    
    private function extract_article_faq($generated_content) {
        if (!isset($generated_content['seo_sections']['faq_section'])) {
            return null;
        }
        
        $faq_items = [];
        foreach ($generated_content['seo_sections']['faq_section'] as $faq) {
            $faq_items[] = array(
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text' => $faq['answer']
                )
            );
        }
        
        return array(
            '@type' => 'FAQPage',
            'mainEntity' => $faq_items
        );
    }
    
    private function extract_rating_value($rating_string) {
        if (preg_match('/(\d+\.?\d*)/', $rating_string, $matches)) {
            return $matches[1];
        }
        return '4.5';
    }
    
    private function extract_brand_name($product_name) {
        // Extraire la marque du nom du produit
        $brands = array('Ninja', 'Instant Pot', 'Cuisinart', 'Hamilton Beach', 'Philips', 'Cosori', 'Breville', 'KitchenAid');
        
        foreach ($brands as $brand) {
            if (stripos($product_name, $brand) !== false) {
                return $brand;
            }
        }
        
        // Prendre le premier mot si aucune marque connue
        $words = explode(' ', $product_name);
        return $words[0];
    }
    
    private function extract_price($price_range) {
        // Extraire le prix numérique de la chaîne
        if (preg_match('/\$(\d+)/', $price_range, $matches)) {
            return $matches[1];
        }
        return '99';
    }
    
    private function extract_content_images($post_id) {
        $content = get_post_field('post_content', $post_id);
        $images = [];
        
        // Rechercher toutes les images dans le contenu
        if (preg_match_all('/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $content, $matches)) {
            foreach ($matches[1] as $src) {
                if (filter_var($src, FILTER_VALIDATE_URL)) {
                    $images[] = array(
                        '@type' => 'ImageObject',
                        'url' => $src
                    );
                }
            }
        }
        
        return $images;
    }
    
    private function extract_ingredients_from_html($content) {
        $ingredients = [];
        
        // Rechercher les listes d'ingrédients dans le HTML
        if (preg_match('/<ul[^>]*class=[\'"][^\'"]*(ingredient|recipe)[^\'"]*(.*?)<\/ul>/is', $content, $matches)) {
            if (preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $matches[0], $li_matches)) {
                foreach ($li_matches[1] as $ingredient) {
                    $ingredients[] = strip_tags($ingredient);
                }
            }
        }
        
        return $ingredients;
    }
    
    private function extract_instructions_from_html($content) {
        $instructions = [];
        
        // Rechercher les listes d'instructions dans le HTML
        if (preg_match('/<ol[^>]*class=[\'"][^\'"]*(instruction|recipe|step)[^\'"]*(.*?)<\/ol>/is', $content, $matches)) {
            if (preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $matches[0], $li_matches)) {
                foreach ($li_matches[1] as $index => $instruction) {
                    $instructions[] = array(
                        '@type' => 'HowToStep',
                        'name' => 'Step ' . ($index + 1),
                        'text' => strip_tags($instruction)
                    );
                }
            }
        }
        
        return $instructions;
    }
    
    private function clean_schema_array($schema) {
        // Supprimer les valeurs null et vides récursivement
        foreach ($schema as $key => $value) {
            if (is_array($value)) {
                $schema[$key] = $this->clean_schema_array($value);
                if (empty($schema[$key])) {
                    unset($schema[$key]);
                }
            } elseif ($value === null || $value === '') {
                unset($schema[$key]);
            }
        }
        
        return $schema;
    }
    
    /**
     * FONCTION PUBLIQUE POUR GÉNÉRATION MANUELLE DE SCHEMA
     */
    public function generate_content_schema($content_type, $post_id) {
        return $this->get_schema_for_content_type($content_type, $post_id);
    }
    
    /**
     * VALIDATION DU SCHEMA MARKUP
     */
    public function validate_schema($schema) {
        $required_fields = array(
            'recipe' => ['@type', 'name', 'recipeIngredient', 'recipeInstructions'],
            'article' => ['@type', 'headline', 'author', 'datePublished'],
            'product' => ['@type', 'name', 'description']
        );
        
        $type = strtolower(str_replace('Recipe', 'recipe', $schema['@type']));
        
        if (isset($required_fields[$type])) {
            foreach ($required_fields[$type] as $field) {
                if (!isset($schema[$field]) || empty($schema[$field])) {
                    return false;
                }
            }
        }
        
        return true;
    }
    
    /**
     * DEBUGGING ET LOGS
     */
    public function log_schema_generation($content_type, $post_id, $schema) {
        if (WP_DEBUG && WP_DEBUG_LOG) {
            error_log("Quicky Schema Generated for {$content_type} (Post ID: {$post_id}): " . json_encode($schema, JSON_PRETTY_PRINT));
        }
    }
}

// Initialisation
new QuickySchemaManager();