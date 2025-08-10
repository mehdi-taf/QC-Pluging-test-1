/**
 * Quicky Cooking Admin JavaScript
 * Interface d'administration moderne et interactive
 * Version: 1.0.0
 */

(function($) {
    'use strict';

    // Variables globales admin
    let currentTab = 'api-config';
    let generationInProgress = false;
    let apiTestInProgress = false;

    // Initialisation
    $(document).ready(function() {
        initializeAdminInterface();
        initializeTabNavigation();
        initializeFormHandlers();
        initializeAPIFeatures();
        initializeContentGeneration();
        initializeDashboardFeatures();
        
        console.log('🚀 Quicky Admin Interface initialized');
    });

    /**
     * Initialise l'interface admin générale
     */
    function initializeAdminInterface() {
        // Animations d'entrée pour les cards
        $('.stat-card, .settings-card, .postbox').each(function(index) {
            $(this).css({
                opacity: 0,
                transform: 'translateY(20px)'
            }).delay(index * 100).animate({
                opacity: 1
            }, 500).css({
                transform: 'translateY(0)'
            });
        });

        // Effets de hover avancés
        $('.stat-card').on('mouseenter', function() {
            $(this).find('.stat-number').css('transform', 'scale(1.1)');
        }).on('mouseleave', function() {
            $(this).find('.stat-number').css('transform', 'scale(1)');
        });

        // Auto-save indicateur
        addAutoSaveIndicator();
    }

    /**
     * Gestion de la navigation par onglets
     */
    function initializeTabNavigation() {
        // Navigation principale (settings)
        $('.quicky-nav-tabs .nav-tab').on('click', function(e) {
            e.preventDefault();
            const targetTab = $(this).attr('data-tab');
            switchToTab(targetTab);
        });

        // Navigation des prompts
        $('.prompt-tab').on('click', function() {
            const targetPrompt = $(this).attr('data-prompt');
            switchToPrompt(targetPrompt);
        });

        // Sauvegarder l'onglet actuel
        $(window).on('beforeunload', function() {
            localStorage.setItem('quicky-admin-current-tab', currentTab);
        });

        // Restaurer l'onglet
        const savedTab = localStorage.getItem('quicky-admin-current-tab');
        if (savedTab && $('.nav-tab[data-tab="' + savedTab + '"]').length) {
            switchToTab(savedTab);
        }
    }

    /**
     * Basculer vers un onglet
     */
    function switchToTab(tabId) {
        if (generationInProgress) {
            showAdminNotification('Génération en cours, veuillez patienter...', 'warning');
            return;
        }

        currentTab = tabId;
        
        // Animation de sortie
        $('.tab-content.active').removeClass('active').fadeOut(200, function() {
            // Animation d'entrée
            $('#' + tabId).addClass('active').fadeIn(200);
        });
        
        // Mise à jour navigation
        $('.nav-tab').removeClass('nav-tab-active');
        $('.nav-tab[data-tab="' + tabId + '"]').addClass('nav-tab-active');

        // Analytics
        if (typeof gtag !== 'undefined') {
            gtag('event', 'admin_tab_switch', {
                tab_name: tabId
            });
        }

        console.log('📋 Switched to tab:', tabId);
    }

    /**
     * Basculer vers un prompt
     */
    function switchToPrompt(promptId) {
        $('.prompt-tab').removeClass('active');
        $('.prompt-tab[data-prompt="' + promptId + '"]').addClass('active');
        
        $('.prompt-content').removeClass('active').hide();
        $('#prompt-' + promptId).addClass('active').fadeIn(300);
    }

    /**
     * Initialise les gestionnaires de formulaires
     */
    function initializeFormHandlers() {
        // Auto-save pour les champs importants
        let saveTimeout;
        $('input[name="quicky_ai_api_key"], select[name="quicky_ai_model"]').on('change', function() {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(autoSaveSettings, 2000);
        });

        // Validation en temps réel
        $('input[type="url"]').on('blur', function() {
            validateURL(this);
        });

        // Compteur de caractères pour les prompts
        $('textarea[name*="prompt"]').each(function() {
            addCharacterCounter(this);
        });

        // Sauvegarde avec animation
        $('.quicky-settings-form').on('submit', function(e) {
            const $form = $(this);
            const $submitBtn = $form.find('input[type="submit"]');
            
            // Animation du bouton
            $submitBtn.prop('disabled', true)
                     .val('💾 Sauvegarde...')
                     .css('background', 'linear-gradient(45deg, #28a745, #20c997)');
            
            // Simuler un délai pour l'effet
            setTimeout(() => {
                $submitBtn.val('✅ Sauvegardé!')
                         .css('background', '#28a745');
                
                setTimeout(() => {
                    $submitBtn.prop('disabled', false)
                             .val('💾 Sauvegarder tous les paramètres')
                             .css('background', '');
                }, 2000);
            }, 500);
        });

        console.log('📝 Form handlers initialized');
    }

    /**
     * Auto-sauvegarde des paramètres
     */
    function autoSaveSettings() {
        const apiKey = $('input[name="quicky_ai_api_key"]').val();
        const model = $('select[name="quicky_ai_model"]').val();
        
        if (!apiKey) return;

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'quicky_auto_save',
                api_key: apiKey,
                model: model,
                nonce: QuickyAdmin.nonce
            },
            success: function() {
                showAutoSaveIndicator('saved');
            },
            error: function() {
                showAutoSaveIndicator('error');
            }
        });
    }

    /**
     * Ajoute un indicateur d'auto-save
     */
    function addAutoSaveIndicator() {
        const indicator = $('<div id="autosave-indicator" style="position: fixed; top: 32px; right: 20px; z-index: 9999; display: none;"></div>');
        $('body').append(indicator);
    }

    /**
     * Affiche l'indicateur d'auto-save
     */
    function showAutoSaveIndicator(status) {
        const $indicator = $('#autosave-indicator');
        const statusConfig = {
            saving: { text: '💾 Sauvegarde...', bg: '#ffc107', color: '#212529' },
            saved: { text: '✅ Sauvegardé', bg: '#28a745', color: 'white' },
            error: { text: '❌ Erreur', bg: '#dc3545', color: 'white' }
        };
        
        const config = statusConfig[status];
        
        $indicator.text(config.text)
                 .css({
                     background: config.bg,
                     color: config.color,
                     padding: '8px 15px',
                     borderRadius: '20px',
                     fontSize: '12px',
                     fontWeight: '600',
                     boxShadow: '0 2px 10px rgba(0,0,0,0.2)'
                 })
                 .fadeIn(300);
        
        setTimeout(() => {
            $indicator.fadeOut(300);
        }, 2000);
    }

    /**
     * Initialise les fonctionnalités API
     */
    function initializeAPIFeatures() {
        // Test API
        $(document).on('click', '#test-api-btn', function(e) {
            e.preventDefault();
            testAPIConnection();
        });

        // Toggle visibilité API key
        window.toggleApiKeyVisibility = function() {
            const $input = $('#quicky_ai_api_key');
            const $btn = $('.show-hide-api-key');
            
            if ($input.attr('type') === 'password') {
                $input.attr('type', 'text');
                $btn.html('🙈 Hide');
            } else {
                $input.attr('type', 'password');
                $btn.html('👁️ Show');
            }
        };

        // Validation API key format
        $('#quicky_ai_api_key').on('input', function() {
            const value = $(this).val();
            const isValid = value.startsWith('sk-or-v1-') || value === '';
            
            $(this).css('border-color', isValid ? '' : '#dc3545');
            
            if (!isValid && value.length > 10) {
                showAdminNotification('Format de clé API invalide. Doit commencer par "sk-or-v1-"', 'error');
            }
        });

        console.log('🔑 API features initialized');
    }

    /**
     * Test la connexion API
     */
    function testAPIConnection() {
        if (apiTestInProgress) return;
        
        const $btn = $('#test-api-btn');
        const $result = $('#api-test-result');
        const apiKey = $('#quicky_ai_api_key').val();
        
        if (!apiKey) {
            $result.html('<div style="color: #dc3545;">❌ Veuillez saisir une clé API</div>');
            return;
        }

        apiTestInProgress = true;
        
        $btn.prop('disabled', true)
            .html('🔄 Test en cours...');
        
        $result.html('<div style="color: #6c757d;">⏳ Test de connexion...</div>');

        // Simuler le test API
        setTimeout(() => {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'test_quicky_api',
                    api_key: apiKey,
                    model: $('#quicky_ai_model').val(),
                    nonce: QuickyAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $result.html(`
                            <div style="color: #28a745;">
                                ✅ Connexion réussie!
                                <br><small>Modèle: ${response.data.model}</small>
                            </div>
                        `);
                        celebrateAPISuccess();
                    } else {
                        $result.html(`<div style="color: #dc3545;">❌ ${response.data}</div>`);
                    }
                },
                error: function() {
                    $result.html('<div style="color: #dc3545;">❌ Erreur de connexion</div>');
                },
                complete: function() {
                    $btn.prop('disabled', false)
                        .html('🔍 Tester la connexion');
                    apiTestInProgress = false;
                }
            });
        }, 1000);
    }

    /**
     * Célébration du succès API
     */
    function celebrateAPISuccess() {
        // Petit effet confetti
        createAdminConfetti('#api-test-result', 10);
        
        // Notification
        showAdminNotification('🎉 API connectée avec succès!', 'success');
    }

    /**
     * Initialise la génération de contenu
     */
    function initializeContentGeneration() {
        // Sélecteur de type de contenu
        $(document).on('change', '#content-type', function() {
            const contentType = $(this).val();
            loadContentFields(contentType);
        });

        // Bouton de génération principal
        $(document).on('click', '#generate-quicky-content', function(e) {
            e.preventDefault();
            generateContent();
        });

        // Génération depuis les meta boxes
        $(document).on('click', '#generate-quicky-content-btn', function(e) {
            e.preventDefault();
            generateContentFromMetaBox();
        });

        console.log('🤖 Content generation initialized');
    }

    /**
     * Charge les champs de contenu dynamiquement
     */
    function loadContentFields(contentType) {
        const $container = $('#content-fields-container, #quicky-generation-fields');
        const $generateBtn = $('#generate-quicky-content, #generate-quicky-content-btn');
        
        if (!contentType) {
            $container.html('<div style="text-align: center; padding: 40px; color: #666;">👆 Sélectionnez un type de contenu pour continuer</div>');
            $generateBtn.prop('disabled', true);
            return;
        }

        $container.html('<div style="text-align: center; padding: 40px;"><div class="spinner is-active"></div> Chargement des champs...</div>');

        // Animation de chargement
        setTimeout(() => {
            let fieldsHTML = getFieldsHTML(contentType);
            
            $container.html(fieldsHTML).hide().fadeIn(500);
            $generateBtn.prop('disabled', false);
            
            // Initialiser les nouveaux champs
            initializeFormFields($container);
            
        }, 800);
    }

    /**
     * Récupère le HTML des champs selon le type
     */
    function getFieldsHTML(contentType) {
        const fields = {
            recipe: getRecipeFieldsHTML(),
            'buying-guide': getBuyingGuideFieldsHTML(),
            comparison: getComparisonFieldsHTML(),
            'blog-article': getBlogArticleFieldsHTML()
        };
        
        return fields[contentType] || '';
    }

    /**
     * HTML pour les champs recette
     */
    function getRecipeFieldsHTML() {
        return `
            <div class="content-generation-form">
                <div class="form-grid">
                    <div class="form-section">
                        <h3>🍳 Informations de la recette</h3>
                        
                        <div class="form-group">
                            <label for="recipe_appliance_type">Appareil de cuisine :</label>
                            <select name="appliance_type" id="recipe_appliance_type" class="widefat">
                                <option value="">Sélectionner un appareil...</option>
                                <optgroup label="Appareils populaires">
                                    <option value="air-fryer">🍟 Air Fryer</option>
                                    <option value="instant-pot">⚡ Instant Pot</option>
                                    <option value="slow-cooker">🍲 Slow Cooker</option>
                                    <option value="crockpot">🍲 Crockpot</option>
                                    <option value="toaster-oven">🔥 Toaster Oven</option>
                                </optgroup>
                                <optgroup label="Appareils spécialisés">
                                    <option value="sous-vide">🌡️ Sous-vide</option>
                                    <option value="bread-maker">🍞 Machine à Pain</option>
                                    <option value="rice-cooker">🍚 Rice Cooker</option>
                                    <option value="dehydrator">🥬 Déshydrateur</option>
                                    <option value="stand-mixer">🥧 Stand Mixer</option>
                                </optgroup>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="recipe_keyword">Mot-clé principal :</label>
                            <input type="text" name="recipe_keyword" id="recipe_keyword" 
                                   placeholder="ex: ailes de poulet, gâteau au chocolat..." 
                                   class="widefat" required>
                            <p class="description">Le plat ou ingrédient principal de la recette</p>
                        </div>
                        
                        <div class="form-group">
                            <label for="custom_title">Titre personnalisé (optionnel) :</label>
                            <input type="text" name="custom_title" id="custom_title" 
                                   placeholder="Laissez vide pour génération automatique" 
                                   class="widefat">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="cooking_time">Temps total (min) :</label>
                                <input type="number" name="cooking_time" id="cooking_time" 
                                       value="30" min="5" max="480" class="small-text">
                            </div>
                            <div class="form-group">
                                <label for="serves">Portions :</label>
                                <input type="number" name="serves" id="serves" 
                                       value="4" min="1" max="20" class="small-text">
                            </div>
                            <div class="form-group">
                                <label for="difficulty">Difficulté :</label>
                                <select name="difficulty" id="difficulty">
                                    <option value="easy">🟢 Facile</option>
                                    <option value="medium">🟡 Moyen</option>
                                    <option value="hard">🔴 Difficile</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="cuisine_type">Type de cuisine :</label>
                            <select name="cuisine_type" id="cuisine_type" class="widefat">
                                <option value="">Non spécifié</option>
                                <option value="american">Américaine</option>
                                <option value="italian">Italienne</option>
                                <option value="asian">Asiatique</option>
                                <option value="mexican">Mexicaine</option>
                                <option value="french">Française</option>
                                <option value="mediterranean">Méditerranéenne</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Régimes alimentaires :</label>
                            <div class="checkbox-group">
                                <label><input type="checkbox" name="dietary_tags[]" value="vegan"> Vegan</label>
                                <label><input type="checkbox" name="dietary_tags[]" value="vegetarian"> Végétarien</label>
                                <label><input type="checkbox" name="dietary_tags[]" value="keto"> Keto</label>
                                <label><input type="checkbox" name="dietary_tags[]" value="gluten-free"> Sans gluten</label>
                                <label><input type="checkbox" name="dietary_tags[]" value="dairy-free"> Sans lactose</label>
                                <label><input type="checkbox" name="dietary_tags[]" value="low-carb"> Low-carb</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>💰 Monétisation</h3>
                        
                        <div class="form-group">
                            <label for="primary_affiliate_product">Produit d'affiliation principal :</label>
                            <input type="text" name="primary_affiliate_product" id="primary_affiliate_product" 
                                   placeholder="ex: Ninja Air Fryer XL" 
                                   class="widefat">
                        </div>
                        
                        <div class="form-group">
                            <label for="primary_affiliate_link">Lien d'affiliation :</label>
                            <input type="url" name="primary_affiliate_link" id="primary_affiliate_link" 
                                   placeholder="https://amazon.com/..." 
                                   class="widefat">
                        </div>
                        
                        <div class="form-group">
                            <label for="ai_notes">Notes pour l'IA :</label>
                            <textarea name="ai_notes" id="ai_notes" rows="4" 
                                      placeholder="Instructions spéciales pour la génération..." 
                                      class="widefat"></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="generation-actions">
                    <button type="button" id="preview-generation" class="button button-secondary">
                        👀 Prévisualiser
                    </button>
                    <button type="button" id="generate-content-btn" class="button button-primary button-large">
                        🚀 Générer la recette
                    </button>
                </div>
            </div>
        `;
    }

    /**
     * HTML pour les champs guide d'achat (simplifié pour la démo)
     */
    function getBuyingGuideFieldsHTML() {
        return `
            <div class="content-generation-form">
                <div class="form-section">
                    <h3>📖 Guide d'achat</h3>
                    <div class="form-group">
                        <label>Catégorie de produit :</label>
                        <input type="text" name="product_category" placeholder="ex: Air Fryers" class="widefat" required>
                    </div>
                    <div class="form-group">
                        <label>Gamme de prix :</label>
                        <select name="budget_range" class="widefat">
                            <option value="budget">Budget (< $100)</option>
                            <option value="mid-range">Milieu ($100-300)</option>
                            <option value="premium">Premium ($300+)</option>
                        </select>
                    </div>
                </div>
                <div class="generation-actions">
                    <button type="button" class="button button-primary">🚀 Générer le guide</button>
                </div>
            </div>
        `;
    }

    /**
     * HTML pour les champs comparatif (simplifié)
     */
    function getComparisonFieldsHTML() {
        return `
            <div class="content-generation-form">
                <div class="form-section">
                    <h3>⚖️ Comparatif</h3>
                    <div class="form-group">
                        <label>Produits à comparer :</label>
                        <textarea name="products_to_compare" rows="4" placeholder="Produit 1 vs Produit 2" class="widefat"></textarea>
                    </div>
                </div>
                <div class="generation-actions">
                    <button type="button" class="button button-primary">🚀 Générer le comparatif</button>
                </div>
            </div>
        `;
    }

    /**
     * HTML pour les champs article (simplifié)
     */
    function getBlogArticleFieldsHTML() {
        return `
            <div class="content-generation-form">
                <div class="form-section">
                    <h3>📝 Article de blog</h3>
                    <div class="form-group">
                        <label>Sujet principal :</label>
                        <input type="text" name="main_topic" placeholder="ex: organisation cuisine" class="widefat" required>
                    </div>
                </div>
                <div class="generation-actions">
                    <button type="button" class="button button-primary">🚀 Générer l'article</button>
                </div>
            </div>
        `;
    }

    /**
     * Initialise les nouveaux champs du formulaire
     */
    function initializeFormFields($container) {
        // Validation en temps réel
        $container.find('input[required]').on('blur', function() {
            const $this = $(this);
            if (!$this.val().trim()) {
                $this.css('border-color', '#dc3545');
                showFieldError($this, 'Ce champ est requis');
            } else {
                $this.css('border-color', '');
                hideFieldError($this);
            }
        });

        // Auto-suggestions pour les mots-clés
        $container.find('#recipe_keyword').on('input', function() {
            const value = $(this).val();
            if (value.length > 2) {
                showKeywordSuggestions(this, value);
            }
        });

        // Calcul automatique du temps total
        $container.find('#cooking_time').on('input', function() {
            updateTimeEstimation();
        });
    }

    /**
     * Génère le contenu principal
     */
    function generateContent() {
        if (generationInProgress) return;

        const formData = collectFormData();
        if (!validateFormData(formData)) return;

        startGeneration(formData);
    }

    /**
     * Collecte les données du formulaire
     */
    function collectFormData() {
        const data = {};
        
        $('.content-generation-form input, .content-generation-form select, .content-generation-form textarea').each(function() {
            const $field = $(this);
            const name = $field.attr('name');
            const value = $field.val();
            
            if ($field.attr('type') === 'checkbox') {
                if (!data[name]) data[name] = [];
                if ($field.is(':checked')) {
                    data[name].push(value);
                }
            } else {
                data[name] = value;
            }
        });
        
        return data;
    }

    /**
     * Valide les données du formulaire
     */
    function validateFormData(data) {
        const errors = [];
        
        // Validation selon le type de contenu
        const contentType = $('#content-type').val() || $('#quicky-content-type').val();
        
        if (contentType === 'recipe') {
            if (!data.recipe_keyword) errors.push('Le mot-clé de recette est requis');
            if (!data.appliance_type) errors.push('L\'appareil de cuisine est requis');
        }
        
        if (errors.length > 0) {
            showAdminNotification('Erreurs de validation: ' + errors.join(', '), 'error');
            return false;
        }
        
        return true;
    }

    /**
     * Démarre la génération avec animations
     */
    function startGeneration(formData) {
        generationInProgress = true;
        
        // UI Updates
        const $generateBtn = $('#generate-content-btn, #generate-quicky-content-btn');
        const $status = $('#generation-status, #quicky-generation-status');
        
        $generateBtn.prop('disabled', true)
                   .html('🔄 Génération en cours...');
        
        $status.removeClass('hidden')
               .find('.status-message')
               .html('🤖 L\'IA travaille sur votre contenu...');
        
        // Animation de la barre de progression
        animateProgressBar(0, 100, 10000);
        
        // Appel AJAX
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'generate_quicky_content',
                content_data: formData,
                content_type: $('#content-type').val() || $('#quicky-content-type').val(),
                nonce: QuickyAdmin.nonce
            },
            timeout: 120000, // 2 minutes
            success: function(response) {
                handleGenerationSuccess(response);
            },
            error: function(xhr, status, error) {
                handleGenerationError(error);
            },
            complete: function() {
                completeGeneration();
            }
        });
    }

    /**
     * Anime la barre de progression
     */
    function animateProgressBar(start, end, duration) {
        const $progressBar = $('.progress-fill');
        const startTime = Date.now();
        
        function updateProgress() {
            const elapsed = Date.now() - startTime;
            const progress = Math.min(start + (end - start) * (elapsed / duration), end);
            
            $progressBar.css('width', progress + '%');
            
            if (progress < end && generationInProgress) {
                requestAnimationFrame(updateProgress);
            }
        }
        
        updateProgress();
    }

    /**
     * Gère le succès de la génération
     */
    function handleGenerationSuccess(response) {
        if (response.success) {
            $('.status-message').html('✅ Contenu généré avec succès!');
            $('.progress-fill').css('width', '100%').addClass('progress-complete');
            
            // Redirection ou mise à jour
            if (response.data.edit_link) {
                setTimeout(() => {
                    showAdminNotification('🎉 Redirection vers l\'éditeur...', 'success');
                    window.location.href = response.data.edit_link;
                }, 2000);
            }
            
            // Confetti de célébration
            createAdminConfetti('.generation-actions', 20);
            
        } else {
            handleGenerationError(response.data || 'Erreur inconnue');
        }
    }

    /**
     * Gère les erreurs de génération
     */
    function handleGenerationError(error) {
        $('.status-message').html('❌ Erreur: ' + error);
        $('.progress-fill').css('background', '#dc3545');
        
        showAdminNotification('Erreur de génération: ' + error, 'error');
        
        console.error('Generation error:', error);
    }

    /**
     * Finalise la génération
     */
    function completeGeneration() {
        generationInProgress = false;
        
        const $generateBtn = $('#generate-content-btn, #generate-quicky-content-btn');
        
        setTimeout(() => {
            $generateBtn.prop('disabled', false)
                       .html('🚀 Générer le contenu');
            
            $('.progress-fill').css('width', '0%')
                              .removeClass('progress-complete')
                              .css('background', '');
            
            $('#generation-status, #quicky-generation-status').addClass('hidden');
        }, 3000);
    }

    /**
     * Initialise les fonctionnalités du dashboard
     */
    function initializeDashboardFeatures() {
        // Actualisation des stats en temps réel
        setInterval(updateDashboardStats, 30000); // 30 secondes
        
        // Animation des chiffres
        animateCounters();
        
        // Graphiques simples (si nécessaire)
        initializeCharts();
        
        console.log('📊 Dashboard features initialized');
    }

    /**
     * Met à jour les statistiques du dashboard
     */
    function updateDashboardStats() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'get_quicky_stats',
                nonce: QuickyAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    updateStatCards(response.data);
                }
            }
        });
    }

    /**
     * Met à jour les cartes de statistiques
     */
    function updateStatCards(stats) {
        $('.stat-card.recipes .stat-number').text(stats.recipes || 0);
        $('.stat-card.guides .stat-number').text(stats.guides || 0);
        $('.stat-card.comparisons .stat-number').text(stats.comparisons || 0);
        $('.stat-card.seo-score .stat-number').text(stats.avg_seo_score || 0);
    }

    /**
     * Anime les compteurs
     */
    function animateCounters() {
        $('.stat-number').each(function() {
            const $this = $(this);
            const targetValue = parseInt($this.text()) || 0;
            
            $({ value: 0 }).animate({ value: targetValue }, {
                duration: 2000,
                easing: 'swing',
                step: function() {
                    $this.text(Math.floor(this.value));
                },
                complete: function() {
                    $this.text(targetValue);
                }
            });
        });
    }

    /**
     * Initialise les graphiques (placeholder)
     */
    function initializeCharts() {
        // Placeholder pour futurs graphiques
        // Peut être étendu avec Chart.js ou D3.js
    }

    /**
     * Fonctions utilitaires
     */

    /**
     * Affiche une notification admin
     */
    function showAdminNotification(message, type = 'info', duration = 5000) {
        const typeClasses = {
            info: 'notice-info',
            success: 'notice-success',
            warning: 'notice-warning',
            error: 'notice-error'
        };
        
        const $notice = $(`
            <div class="notice ${typeClasses[type]} is-dismissible quicky-admin-notice">
                <p><strong>${message}</strong></p>
                <button type="button" class="notice-dismiss">
                    <span class="screen-reader-text">Dismiss this notice.</span>
                </button>
            </div>
        `);
        
        // Insérer après le titre de page
        $('.wrap h1').first().after($notice);
        
        // Animation d'entrée
        $notice.hide().slideDown(300);
        
        // Auto-dismiss
        setTimeout(() => {
            $notice.slideUp(300, () => $notice.remove());
        }, duration);
        
        // Click pour fermer
        $notice.find('.notice-dismiss').on('click', () => {
            $notice.slideUp(300, () => $notice.remove());
        });
    }

    /**
     * Crée un effet confetti admin
     */
    function createAdminConfetti(target, count = 15) {
        const $target = $(target);
        const targetOffset = $target.offset();
        const colors = ['#ff6b35', '#4ecdc4', '#ffe66d', '#ff6b9d', '#95e1d3'];
        
        for (let i = 0; i < count; i++) {
            const confetti = $('<div>')
                .css({
                    position: 'absolute',
                    width: '6px',
                    height: '6px',
                    background: colors[Math.floor(Math.random() * colors.length)],
                    borderRadius: '50%',
                    left: targetOffset.left + $target.width()/2,
                    top: targetOffset.top + $target.height()/2,
                    pointerEvents: 'none',
                    zIndex: 9999
                })
                .appendTo('body');
            
            // Animation
            const angle = (Math.PI * 2 * i) / count;
            const velocity = 20 + Math.random() * 20;
            
            confetti.animate({
                left: '+=' + (Math.cos(angle) * velocity),
                top: '+=' + (Math.sin(angle) * velocity),
                opacity: 0
            }, 1000, function() {
                $(this).remove();
            });
        }
    }

    /**
     * Affiche les erreurs de champ
     */
    function showFieldError($field, message) {
        hideFieldError($field);
        
        const $error = $('<div class="field-error">' + message + '</div>')
            .css({
                color: '#dc3545',
                fontSize: '12px',
                marginTop: '5px'
            });
        
        $field.after($error);
    }

    /**
     * Cache les erreurs de champ
     */
    function hideFieldError($field) {
        $field.next('.field-error').remove();
    }

    /**
     * Valide une URL
     */
    function validateURL(input) {
        const $input = $(input);
        const value = $input.val();
        
        if (value && !isValidURL(value)) {
            $input.css('border-color', '#dc3545');
            showFieldError($input, 'URL invalide');
        } else {
            $input.css('border-color', '');
            hideFieldError($input);
        }
    }

    /**
     * Vérifie si une URL est valide
     */
    function isValidURL(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }

    /**
     * Ajoute un compteur de caractères
     */
    function addCharacterCounter(textarea) {
        const $textarea = $(textarea);
        const maxLength = 5000;
        
        const $counter = $('<div class="char-counter">')
            .css({
                textAlign: 'right',
                fontSize: '12px',
                color: '#6c757d',
                marginTop: '5px'
            });
        
        $textarea.after($counter);
        
        function updateCounter() {
            const length = $textarea.val().length;
            const remaining = maxLength - length;
            
            $counter.text(length + '/' + maxLength + ' caractères');
            
            if (remaining < 100) {
                $counter.css('color', '#dc3545');
            } else if (remaining < 500) {
                $counter.css('color', '#ffc107');
            } else {
                $counter.css('color', '#6c757d');
            }
        }
        
        $textarea.on('input', updateCounter);
        updateCounter();
    }

    // Fonctions globales pour compatibilité
    window.resetPromptDefaults = function() {
        if (confirm('Êtes-vous sûr de vouloir restaurer tous les prompts par défaut ? Cette action est irréversible.')) {
            $('.prompt-content textarea').each(function() {
                $(this).val(''); // Reset, puis AJAX pour récupérer les défauts
            });
            
            showAdminNotification('Prompts restaurés aux valeurs par défaut', 'success');
        }
    };

    window.testCurrentPrompt = function() {
        const activePrompt = $('.prompt-content.active textarea').val();
        if (!activePrompt) {
            showAdminNotification('Aucun prompt à tester', 'warning');
            return;
        }
        
        showAdminNotification('Test du prompt en cours...', 'info');
        // Logique de test ici
    };

    window.exportSettings = function() {
        showAdminNotification('Export des paramètres...', 'info');
        // Logique d'export
    };

    window.importSettings = function() {
        showAdminNotification('Import des paramètres...', 'info');
        // Logique d'import
    };

    window.clearCache = function() {
        if (confirm('Vider le cache du plugin ?')) {
            showAdminNotification('Cache vidé', 'success');
        }
    };

    // Log de fin
    console.log('✅ Quicky Admin JavaScript fully loaded');

})(jQuery);