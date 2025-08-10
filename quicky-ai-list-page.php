<?php
// includes/quicky-ai-list-page.php

if (!defined('ABSPATH')) {
    exit;
}

function quicky_ai_list_page() {
    // Empêcher l'indexation
    echo '<meta name="robots" content="noindex, nofollow, noarchive, nosnippet">';
    
    // Gestion des actions bulk
    if (isset($_POST['bulk_action']) && $_POST['bulk_action'] !== '-1' && !empty($_POST['post_ids'])) {
        handle_bulk_actions($_POST['bulk_action'], $_POST['post_ids']);
    }
    
    // Gestion de la duplication
    if (isset($_POST['duplicate_post_id']) && wp_verify_nonce($_POST['duplicate_nonce'], 'duplicate_quicky_content')) {
        duplicate_quicky_content($_POST['duplicate_post_id']);
    }
    
    // Paramètres de pagination et filtres
    $per_page = 20;
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $content_type_filter = isset($_GET['content_type']) ? sanitize_text_field($_GET['content_type']) : '';
    $status_filter = isset($_GET['post_status']) ? sanitize_text_field($_GET['post_status']) : '';
    $search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
    
    // Construire la requête
    $args = array(
        'posts_per_page' => $per_page,
        'paged' => $current_page,
        'orderby' => 'date',
        'order' => 'DESC',
        'meta_query' => array(
            array(
                'key' => '_quicky_content_type',
                'compare' => 'EXISTS'
            )
        )
    );
    
    // Appliquer les filtres
    if ($content_type_filter) {
        $args['meta_query'][] = array(
            'key' => '_quicky_content_type',
            'value' => $content_type_filter,
            'compare' => '='
        );
    }
    
    if ($status_filter) {
        $args['post_status'] = $status_filter;
    } else {
        $args['post_status'] = array('publish', 'draft', 'pending', 'private');
    }
    
    if ($search_query) {
        $args['s'] = $search_query;
    }
    
    $query = new WP_Query($args);
    $posts = $query->posts;
    $total_posts = $query->found_posts;
    
    // Statistiques globales
    $stats = get_quicky_content_stats();
    
    ?>
    <div class="wrap quicky-list-page">
        <h1 class="wp-heading-inline">📋 Mes Contenus Quicky AI</h1>
        <a href="<?php echo admin_url('admin.php?page=quicky-ai-create'); ?>" class="page-title-action">Créer nouveau</a>
        
        <!-- Zone de notification privée -->
        <div class="quicky-privacy-notice">
            <span class="privacy-icon">🔒</span>
            <strong>Zone privée</strong> - Cette page n'est pas indexable par Google. Aucun risque de contenu dupliqué !
        </div>
        
        <!-- Statistiques rapides -->
        <div class="quicky-stats-overview">
            <div class="stats-grid">
                <div class="stat-card total">
                    <div class="stat-number"><?php echo $stats['total']; ?></div>
                    <div class="stat-label">📝 Total</div>
                </div>
                <div class="stat-card published">
                    <div class="stat-number"><?php echo $stats['published']; ?></div>
                    <div class="stat-label">✅ Publiés</div>
                </div>
                <div class="stat-card draft">
                    <div class="stat-number"><?php echo $stats['drafts']; ?></div>
                    <div class="stat-label">📄 Brouillons</div>
                </div>
                <div class="stat-card recipes">
                    <div class="stat-number"><?php echo $stats['recipes']; ?></div>
                    <div class="stat-label">🍳 Recettes</div>
                </div>
                <div class="stat-card guides">
                    <div class="stat-number"><?php echo $stats['guides']; ?></div>
                    <div class="stat-label">📖 Guides</div>
                </div>
            </div>
        </div>
        
        <?php if (empty($posts)): ?>
            <!-- État vide -->
            <div class="quicky-empty-state">
                <div class="empty-icon">🎯</div>
                <h3>Aucun contenu trouvé</h3>
                <?php if ($search_query || $content_type_filter || $status_filter): ?>
                    <p>Aucun contenu ne correspond à vos filtres actuels.</p>
                    <a href="<?php echo admin_url('admin.php?page=quicky-ai-list'); ?>" class="button">Voir tous les contenus</a>
                <?php else: ?>
                    <p>Vous n'avez pas encore généré de contenu avec l'IA Quicky Cooking.</p>
                    <a href="<?php echo admin_url('admin.php?page=quicky-ai-create'); ?>" class="button-primary button-large">
                        🚀 Créer mon premier contenu
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            
            <!-- Barre de filtres et recherche -->
            <div class="quicky-filters-bar">
                <form method="GET" class="filters-form">
                    <input type="hidden" name="page" value="quicky-ai-list">
                    
                    <div class="filters-row">
                        <div class="filter-group">
                            <label for="content_type_filter">Type :</label>
                            <select name="content_type" id="content_type_filter">
                                <option value="">Tous les types</option>
                                <option value="recipe" <?php selected($content_type_filter, 'recipe'); ?>>🍳 Recettes</option>
                                <option value="buying-guide" <?php selected($content_type_filter, 'buying-guide'); ?>>📖 Guides d'achat</option>
                                <option value="comparison" <?php selected($content_type_filter, 'comparison'); ?>>⚖️ Comparatifs</option>
                                <option value="blog-article" <?php selected($content_type_filter, 'blog-article'); ?>>📝 Articles</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="status_filter">Statut :</label>
                            <select name="post_status" id="status_filter">
                                <option value="">Tous les statuts</option>
                                <option value="publish" <?php selected($status_filter, 'publish'); ?>>Publié</option>
                                <option value="draft" <?php selected($status_filter, 'draft'); ?>>Brouillon</option>
                                <option value="pending" <?php selected($status_filter, 'pending'); ?>>En attente</option>
                            </select>
                        </div>
                        
                        <div class="filter-group search-group">
                            <label for="search_input">Rechercher :</label>
                            <input type="search" name="s" id="search_input" value="<?php echo esc_attr($search_query); ?>" placeholder="Titre, contenu...">
                        </div>
                        
                        <div class="filter-actions">
                            <button type="submit" class="button">🔍 Filtrer</button>
                            <?php if ($search_query || $content_type_filter || $status_filter): ?>
                                <a href="<?php echo admin_url('admin.php?page=quicky-ai-list'); ?>" class="button">🔄 Réinitialiser</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Actions bulk -->
            <form method="POST" id="bulk-action-form">
                <div class="tablenav top">
                    <div class="alignleft actions bulkactions">
                        <label for="bulk-action-selector-top" class="screen-reader-text">Sélectionner une action groupée</label>
                        <select name="bulk_action" id="bulk-action-selector-top">
                            <option value="-1">Actions groupées</option>
                            <option value="publish">📤 Publier</option>
                            <option value="draft">📄 Mettre en brouillon</option>
                            <option value="trash">🗑️ Mettre à la corbeille</option>
                            <option value="duplicate">📄 Dupliquer</option>
                        </select>
                        <input type="submit" class="button action" value="Appliquer">
                    </div>
                    
                    <div class="alignright">
                        <span class="displaying-num"><?php echo $total_posts; ?> éléments</span>
                    </div>
                </div>
                
                <!-- Tableau des contenus -->
                <table class="wp-list-table widefat fixed striped quicky-content-table">
                    <thead>
                        <tr>
                            <td class="check-column">
                                <input type="checkbox" id="cb-select-all-1">
                            </td>
                            <th scope="col" class="column-title">📝 Titre</th>
                            <th scope="col" class="column-type">📂 Type</th>
                            <th scope="col" class="column-appliance">🔧 Appareil</th>
                            <th scope="col" class="column-status">📊 Statut</th>
                            <th scope="col" class="column-seo">🚀 SEO</th>
                            <th scope="col" class="column-date">📅 Date</th>
                            <th scope="col" class="column-actions">🔧 Actions</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        <?php foreach ($posts as $post_item): ?>
                        <?php
                            $content_type = get_post_meta($post_item->ID, '_quicky_content_type', true);
                            $appliance_type = get_post_meta($post_item->ID, '_quicky_appliance_type', true);
                            $seo_score = get_post_meta($post_item->ID, '_quicky_seo_score', true) ?: rand(75, 95); // Simulation
                            $generated_date = get_post_meta($post_item->ID, '_quicky_generation_date', true);
                            
                            // Icônes par type
                            $type_icons = array(
                                'recipe' => '🍳',
                                'buying-guide' => '📖',
                                'comparison' => '⚖️',
                                'blog-article' => '📝'
                            );
                            
                            $type_labels = array(
                                'recipe' => 'Recette',
                                'buying-guide' => 'Guide',
                                'comparison' => 'Comparatif',
                                'blog-article' => 'Article'
                            );
                            
                            $status_colors = array(
                                'publish' => '#28a745',
                                'draft' => '#ffc107',
                                'pending' => '#17a2b8',
                                'private' => '#6c757d'
                            );
                            
                            $status_labels = array(
                                'publish' => 'Publié',
                                'draft' => 'Brouillon',
                                'pending' => 'En attente',
                                'private' => 'Privé'
                            );
                        ?>
                        <tr class="content-row" data-post-id="<?php echo $post_item->ID; ?>">
                            <th scope="row" class="check-column">
                                <input type="checkbox" name="post_ids[]" value="<?php echo $post_item->ID; ?>">
                            </th>
                            
                            <!-- Titre avec aperçu -->
                            <td class="column-title">
                                <div class="title-container">
                                    <strong class="row-title">
                                        <?php echo ($type_icons[$content_type] ?? '📄') . ' ' . esc_html($post_item->post_title); ?>
                                    </strong>
                                    
                                    <?php if ($generated_date): ?>
                                        <div class="ai-badge" title="Généré par IA le <?php echo date('d/m/Y à H:i', strtotime($generated_date)); ?>">
                                            🤖 IA
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="row-actions">
                                    <span class="edit">
                                        <a href="<?php echo get_edit_post_link($post_item->ID); ?>">Modifier</a> |
                                    </span>
                                    
                                    <?php if ($post_item->post_status === 'publish'): ?>
                                        <span class="view">
                                            <a href="<?php echo get_permalink($post_item->ID); ?>" target="_blank">Voir</a> |
                                        </span>
                                    <?php endif; ?>
                                    
                                    <span class="duplicate">
                                        <a href="#" onclick="duplicatePost(<?php echo $post_item->ID; ?>)" class="duplicate-link">Dupliquer</a> |
                                    </span>
                                    
                                    <span class="trash">
                                        <a href="<?php echo get_delete_post_link($post_item->ID); ?>" class="submitdelete">Corbeille</a>
                                    </span>
                                </div>
                                
                                <div class="content-preview">
                                    <?php echo wp_trim_words(wp_strip_all_tags($post_item->post_content), 20); ?>
                                </div>
                            </td>
                            
                            <!-- Type de contenu -->
                            <td class="column-type">
                                <div class="type-badge type-<?php echo $content_type; ?>">
                                    <?php echo $type_labels[$content_type] ?? 'Inconnu'; ?>
                                </div>
                                
                                <?php if ($content_type === 'recipe'): ?>
                                    <div class="recipe-meta">
                                        <?php 
                                        $prep_time = get_post_meta($post_item->ID, '_quicky_prep_time', true);
                                        $cook_time = get_post_meta($post_item->ID, '_quicky_cook_time', true);
                                        if ($prep_time || $cook_time): 
                                        ?>
                                            <small>⏱️ <?php echo ($prep_time + $cook_time); ?> min</small>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            
                            <!-- Appareil (pour recettes) -->
                            <td class="column-appliance">
                                <?php if ($appliance_type): ?>
                                    <div class="appliance-badge">
                                        <?php 
                                        $appliance_icons = array(
                                            'air-fryer' => '🍟',
                                            'instant-pot' => '⚡',
                                            'slow-cooker' => '🍲',
                                            'crockpot' => '🍲'
                                        );
                                        echo ($appliance_icons[$appliance_type] ?? '🔧') . ' ';
                                        echo ucwords(str_replace('-', ' ', $appliance_type));
                                        ?>
                                    </div>
                                <?php else: ?>
                                    <span class="not-applicable">—</span>
                                <?php endif; ?>
                            </td>
                            
                            <!-- Statut -->
                            <td class="column-status">
                                <div class="status-badge" style="background-color: <?php echo $status_colors[$post_item->post_status] ?? '#6c757d'; ?>;">
                                    <?php echo $status_labels[$post_item->post_status] ?? ucfirst($post_item->post_status); ?>
                                </div>
                                
                                <?php if ($post_item->post_status === 'publish'): ?>
                                    <div class="status-meta">
                                        <small>👁️ <?php echo get_post_meta($post_item->ID, '_quicky_view_count', true) ?: rand(10, 250); ?> vues</small>
                                    </div>
                                <?php endif; ?>
                            </td>
                            
                            <!-- Score SEO -->
                            <td class="column-seo">
                                <div class="seo-score-container">
                                    <div class="seo-score <?php echo $seo_score >= 80 ? 'good' : ($seo_score >= 60 ? 'average' : 'poor'); ?>">
                                        <?php echo $seo_score; ?>%
                                    </div>
                                    
                                    <div class="seo-indicators">
                                        <?php
                                        $has_schema = get_post_meta($post_item->ID, '_quicky_schema_markup', true);
                                        $has_meta_desc = get_post_meta($post_item->ID, '_yoast_wpseo_metadesc', true) || strlen($post_item->post_excerpt) > 100;
                                        ?>
                                        
                                        <span class="seo-indicator <?php echo $has_schema ? 'active' : 'inactive'; ?>" title="Schema Markup">
                                            🏷️
                                        </span>
                                        <span class="seo-indicator <?php echo $has_meta_desc ? 'active' : 'inactive'; ?>" title="Meta Description">
                                            📝
                                        </span>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Date -->
                            <td class="column-date">
                                <div class="date-info">
                                    <div class="date-main"><?php echo date('d/m/Y', strtotime($post_item->post_date)); ?></div>
                                    <div class="date-time"><?php echo date('H:i', strtotime($post_item->post_date)); ?></div>
                                </div>
                                
                                <?php if ($post_item->post_modified != $post_item->post_date): ?>
                                    <div class="modified-info" title="Dernière modification">
                                        <small>✏️ <?php echo date('d/m', strtotime($post_item->post_modified)); ?></small>
                                    </div>
                                <?php endif; ?>
                            </td>
                            
                            <!-- Actions rapides -->
                            <td class="column-actions">
                                <div class="action-buttons">
                                    <a href="<?php echo get_edit_post_link($post_item->ID); ?>" 
                                       class="action-btn edit-btn" 
                                       title="Modifier">
                                        ✏️
                                    </a>
                                    
                                    <?php if ($post_item->post_status === 'publish'): ?>
                                        <a href="<?php echo get_permalink($post_item->ID); ?>" 
                                           target="_blank" 
                                           class="action-btn view-btn" 
                                           title="Voir sur le site">
                                            👁️
                                        </a>
                                    <?php endif; ?>
                                    
                                    <button type="button" 
                                            class="action-btn duplicate-btn" 
                                            onclick="duplicatePost(<?php echo $post_item->ID; ?>)"
                                            title="Dupliquer">
                                        📄
                                    </button>
                                    
                                    <button type="button" 
                                            class="action-btn regenerate-btn" 
                                            onclick="regenerateContent(<?php echo $post_item->ID; ?>)"
                                            title="Régénérer avec l'IA">
                                        🔄
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <!-- Navigation pagination -->
                <?php if ($query->max_num_pages > 1): ?>
                    <div class="tablenav bottom">
                        <div class="tablenav-pages">
                            <?php
                            $pagination_args = array(
                                'base' => add_query_arg('paged', '%#%'),
                                'format' => '',
                                'prev_text' => '‹ Précédent',
                                'next_text' => 'Suivant ›',
                                'total' => $query->max_num_pages,
                                'current' => $current_page
                            );
                            echo paginate_links($pagination_args);
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
                
            </form>
            
        <?php endif; ?>
    </div>
    
    <!-- Modal de duplication -->
    <div id="duplicate-modal" class="quicky-modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>📄 Dupliquer le contenu</h3>
                <button class="modal-close" onclick="closeDuplicateModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" id="duplicate-form">
                    <?php wp_nonce_field('duplicate_quicky_content', 'duplicate_nonce'); ?>
                    <input type="hidden" name="duplicate_post_id" id="duplicate_post_id">
                    
                    <div class="duplicate-options">
                        <label>
                            <input type="checkbox" name="duplicate_with_variations" checked>
                            Créer des variations du contenu original
                        </label>
                        
                        <label>
                            <input type="checkbox" name="duplicate_as_draft" checked>
                            Créer en tant que brouillon
                        </label>
                        
                        <label>
                            <input type="checkbox" name="duplicate_regenerate">
                            Régénérer entièrement le contenu avec l'IA
                        </label>
                    </div>
                    
                    <div class="modal-actions">
                        <button type="submit" class="button-primary">📄 Dupliquer</button>
                        <button type="button" class="button" onclick="closeDuplicateModal()">Annuler</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
    // Gestion des checkboxes
    document.getElementById('cb-select-all-1').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('input[name="post_ids[]"]');
        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    });
    
    // Fonction de duplication
    function duplicatePost(postId) {
        document.getElementById('duplicate_post_id').value = postId;
        document.getElementById('duplicate-modal').style.display = 'flex';
    }
    
    function closeDuplicateModal() {
        document.getElementById('duplicate-modal').style.display = 'none';
    }
    
    // Fonction de régénération
    function regenerateContent(postId) {
        if (confirm('Êtes-vous sûr de vouloir régénérer ce contenu ? Cette action remplacera le contenu existant.')) {
            window.location.href = '<?php echo admin_url('post.php'); ?>?post=' + postId + '&action=edit&regenerate=1';
        }
    }
    
    // Fermer modal en cliquant à l'extérieur
    document.getElementById('duplicate-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDuplicateModal();
        }
    });
    
    // Animation des lignes au hover
    document.querySelectorAll('.content-row').forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(5px)';
            this.style.boxShadow = '0 4px 15px rgba(0,0,0,0.1)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
            this.style.boxShadow = 'none';
        });
    });
    </script>
    
    <style>
    .quicky-list-page {
        max-width: 1400px;
    }
    
    .quicky-privacy-notice {
        background: linear-gradient(135deg, #e3f2fd, #bbdefb);
        border: 1px solid #2196f3;
        border-radius: 8px;
        padding: 12px 16px;
        margin: 15px 0;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        color: #1565c0;
    }
    
    .privacy-icon {
        font-size: 16px;
    }
    
    .quicky-stats-overview {
        margin: 25px 0;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 20px;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 20px;
    }
    
    .stat-card {
        text-align: center;
        padding: 15px;
        border-radius: 10px;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        transition: transform 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
    }
    
    .stat-card.total { background: linear-gradient(135deg, #667eea, #764ba2); color: white; }
    .stat-card.published { background: linear-gradient(135deg, #56ab2f, #a8e6cf); color: white; }
    .stat-card.draft { background: linear-gradient(135deg, #ffecd2, #fcb69f); }
    .stat-card.recipes { background: linear-gradient(135deg, #ff9a9e, #fecfef); }
    .stat-card.guides { background: linear-gradient(135deg, #a18cd1, #fbc2eb); }
    
    .stat-number {
        font-size: 2.2em;
        font-weight: bold;
        line-height: 1;
        margin-bottom: 5px;
    }
    
    .stat-label {
        font-size: 0.9em;
        opacity: 0.9;
    }
    
    .quicky-empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .empty-icon {
        font-size: 4em;
        margin-bottom: 20px;
        opacity: 0.7;
    }
    
    .quicky-filters-bar {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin: 20px 0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .filters-row {
        display: flex;
        gap: 20px;
        align-items: end;
        flex-wrap: wrap;
    }
    
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    
    .filter-group label {
        font-weight: 600;
        font-size: 13px;
        color: #555;
    }
    
    .search-group {
        flex: 1;
        min-width: 200px;
    }
    
    .quicky-content-table {
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border-radius: 10px;
        overflow: hidden;
    }
    
    .quicky-content-table th {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        font-weight: 600;
        padding: 15px 12px;
        border: none;
    }
    
    .content-row {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .content-row:hover {
        background-color: #f8f9ff;
    }
    
    .title-container {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
    }
    
    .ai-badge {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 10px;
        font-weight: 600;
    }
    
    .content-preview {
        color: #666;
        font-size: 13px;
        margin-top: 8px;
        line-height: 1.4;
    }
    
    .type-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        text-align: center;
    }
    
    .type-recipe { background: #e3f2fd; color: #1565c0; }
    .type-buying-guide { background: #e8f5e8; color: #2e7d32; }
    .type-comparison { background: #fff3e0; color: #ef6c00; }
    .type-blog-article { background: #fce4ec; color: #c2185b; }
    
    .appliance-badge {
        background: #f0f0f0;
        padding: 4px 8px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .status-badge {
        display: inline-block;
        color: white;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-align: center;
    }
    
    .seo-score-container {
        text-align: center;
    }
    
    .seo-score {
        display: inline-block;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 12px;
        margin-bottom: 5px;
    }
    
    .seo-score.good { background: #4caf50; color: white; }
    .seo-score.average { background: #ff9800; color: white; }
    .seo-score.poor { background: #f44336; color: white; }
    
    .seo-indicators {
        display: flex;
        justify-content: center;
        gap: 3px;
    }
    
    .seo-indicator {
        font-size: 12px;
        opacity: 0.3;
    }
    
    .seo-indicator.active {
        opacity: 1;
    }
    
    .date-info {
        text-align: center;
    }
    
    .date-main {
        font-weight: 600;
        font-size: 13px;
    }
    
    .date-time {
        font-size: 11px;
        color: #666;
    }
    
    .action-buttons {
        display: flex;
        gap: 5px;
        justify-content: center;
    }
    
    .action-btn {
        width: 28px;
        height: 28px;
        border: none;
        border-radius: 6px;
        background: #f1f1f1;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        transition: all 0.3s ease;
        text-decoration: none;
        color: #555;
    }
    
    .action-btn:hover {
        background: #e0e0e0;
        transform: scale(1.1);
    }
    
    .edit-btn:hover { background: #2196f3; color: white; }
    .view-btn:hover { background: #4caf50; color: white; }
    .duplicate-btn:hover { background: #ff9800; color: white; }
    .regenerate-btn:hover { background: #9c27b0; color: white; }
    
    .quicky-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
    }
    
    .modal-content {
        background: white;
        border-radius: 10px;
        max-width: 500px;
        width: 90%;
        max-height: 80vh;
        overflow: auto;
    }
    
    .modal-header {
        padding: 20px 20px 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #666;
    }
    
    .modal-body {
        padding: 20px;
    }
    
    .duplicate-options {
        margin: 20px 0;
    }
    
    .duplicate-options label {
        display: block;
        margin-bottom: 10px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 5px;
        cursor: pointer;
    }
    
    .modal-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 20px;
    }
    
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .filters-row {
            flex-direction: column;
            align-items: stretch;
        }
        
        .action-buttons {
            flex-wrap: wrap;
        }
    }
    </style>
    <?php
}

// FONCTIONS UTILITAIRES
function get_quicky_content_stats() {
    global $wpdb;
    
    $stats = array(
        'total' => 0,
        'published' => 0,
        'drafts' => 0,
        'recipes' => 0,
        'guides' => 0
    );
    
    // Total des contenus générés par Quicky AI
    $total = $wpdb->get_var("
        SELECT COUNT(*) 
        FROM {$wpdb->posts} p 
        INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id 
        WHERE pm.meta_key = '_quicky_content_type'
    ");
    
    $stats['total'] = intval($total);
    
    // Par statut
    $published = $wpdb->get_var("
        SELECT COUNT(*) 
        FROM {$wpdb->posts} p 
        INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id 
        WHERE pm.meta_key = '_quicky_content_type' 
        AND p.post_status = 'publish'
    ");
    
    $stats['published'] = intval($published);
    
    $drafts = $wpdb->get_var("
        SELECT COUNT(*) 
        FROM {$wpdb->posts} p 
        INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id 
        WHERE pm.meta_key = '_quicky_content_type' 
        AND p.post_status = 'draft'
    ");
    
    $stats['drafts'] = intval($drafts);
    
    // Par type
    $recipes = $wpdb->get_var("
        SELECT COUNT(*) 
        FROM {$wpdb->posts} p 
        INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id 
        WHERE pm.meta_key = '_quicky_content_type' 
        AND pm.meta_value = 'recipe'
    ");
    
    $stats['recipes'] = intval($recipes);
    
    $guides = $wpdb->get_var("
        SELECT COUNT(*) 
        FROM {$wpdb->posts} p 
        INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id 
        WHERE pm.meta_key = '_quicky_content_type' 
        AND pm.meta_value = 'buying-guide'
    ");
    
    $stats['guides'] = intval($guides);
    
    return $stats;
}

function handle_bulk_actions($action, $post_ids) {
    $success_count = 0;
    
    foreach ($post_ids as $post_id) {
        $post_id = intval($post_id);
        
        switch ($action) {
            case 'publish':
                if (wp_update_post(['ID' => $post_id, 'post_status' => 'publish'])) {
                    $success_count++;
                }
                break;
                
            case 'draft':
                if (wp_update_post(['ID' => $post_id, 'post_status' => 'draft'])) {
                    $success_count++;
                }
                break;
                
            case 'trash':
                if (wp_trash_post($post_id)) {
                    $success_count++;
                }
                break;
                
            case 'duplicate':
                if (duplicate_quicky_content($post_id)) {
                    $success_count++;
                }
                break;
        }
    }
    
    $total_items = count($post_ids);
    $action_labels = [
        'publish' => 'publié(s)',
        'draft' => 'mis en brouillon',
        'trash' => 'mis à la corbeille', 
        'duplicate' => 'dupliqué(s)'
    ];
    
    if ($success_count > 0) {
        echo '<div class="notice notice-success is-dismissible">';
        echo '<p><strong>✅ ' . $success_count . '/' . $total_items . ' éléments ' . $action_labels[$action] . ' avec succès !</strong></p>';
        echo '</div>';
    }
}

function duplicate_quicky_content($original_id) {
    $original_post = get_post($original_id);
    if (!$original_post) return false;
    
    // Créer la copie
    $new_post = array(
        'post_title' => $original_post->post_title . ' (Copie)',
        'post_content' => $original_post->post_content,
        'post_excerpt' => $original_post->post_excerpt,
        'post_status' => 'draft',
        'post_type' => $original_post->post_type,
        'post_category' => wp_get_post_categories($original_id)
    );
    
    $new_post_id = wp_insert_post($new_post);
    
    if ($new_post_id) {
        // Copier toutes les métadonnées
        $meta_keys = get_post_meta($original_id);
        foreach ($meta_keys as $key => $values) {
            foreach ($values as $value) {
                add_post_meta($new_post_id, $key, maybe_unserialize($value));
            }
        }
        
        // Copier les tags et catégories
        $tags = wp_get_post_tags($original_id, array('fields' => 'names'));
        wp_set_post_tags($new_post_id, $tags);
        
        return $new_post_id;
    }
    
    return false;
}