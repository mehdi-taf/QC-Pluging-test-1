/**
 * Quicky Cooking Interactions Pro
 * Version: 2.0.0
 * Interactions ultra-avancées pour contenu enrichi
 */

(function($) {
    'use strict';

    // Variables globales
    let quickyInteractions = {
        baseServings: 4,
        currentServings: 4,
        originalIngredients: [],
        completedSteps: [],
        currentStep: 0,
        totalSteps: 0,
        timers: {},
        analytics: {
            startTime: Date.now(),
            interactions: [],
            scrollDepth: 0,
            timeOnPage: 0
        },
        settings: {
            enableHaptics: true,
            enableSounds: true,
            enableAnimations: true,
            autoProgress: false
        }
    };

    // Initialisation
    $(document).ready(function() {
        initializeQuickyInteractionsPro();
        initializeAdvancedFeatures();
        initializeAnalytics();
        initializeKeyboardShortcuts();
    });

    /**
     * INITIALISATION PRINCIPALE
     */
    function initializeQuickyInteractionsPro() {
        console.log('🚀 Initializing Quicky Interactions Pro v2.0');
        
        // Fonctionnalités de base
        initializeServingsAdjustment();
        initializeIngredientInteractions();
        initializeInstructionTracking();
        initializeSmartTimers();
        initializeProgressTracking();
        
        // Fonctionnalités avancées
        initializeVoiceControl();
        initializeGestureControls();
        initializeSmartNotifications();
        initializePersonalization();
        
        // Interface utilisateur
        initializeFloatingControls();
        initializeThemeToggle();
        initializeAccessibilityFeatures();
        
        console.log('✅ Quicky Interactions Pro initialized successfully');
    }

    /**
     * AJUSTEMENT DES PORTIONS INTELLIGENT
     */
    function initializeServingsAdjustment() {
        // Récupérer les portions de base
        const servingsElement = $('#current-servings-storytelling, #current-servings-enhanced');
        if (servingsElement.length) {
            quickyInteractions.baseServings = parseInt(servingsElement.text()) || 4;
            quickyInteractions.currentServings = quickyInteractions.baseServings;
        }

        // Sauvegarder les ingrédients originaux avec parsing intelligent
        $('.ingredient-text-enhanced, .ingredient-name-story').each(function() {
            const originalText = $(this).text();
            const parsedIngredient = parseIngredientText(originalText);
            quickyInteractions.originalIngredients.push(parsedIngredient);
        });

        console.log(`🥄 Servings adjustment initialized: ${quickyInteractions.baseServings} servings`);
    }

    window.adjustServingsStory = window.adjustServingsEnhanced = function(change) {
        const newServings = Math.max(1, Math.min(12, quickyInteractions.currentServings + change));
        
        if (newServings === quickyInteractions.currentServings) {
            showTemporaryMessage('Maximum/minimum servings reached', 'warning');
            return;
        }

        const oldServings = quickyInteractions.currentServings;
        quickyInteractions.currentServings = newServings;
        
        // Animation du changement de portions
        animateServingChange(oldServings, newServings);
        
        // Recalculer et animer les ingrédients
        updateIngredientQuantitiesAdvanced();
        
        // Feedback utilisateur
        triggerHapticFeedback('light');
        playInteractionSound('adjustment');
        
        // Analytics
        trackInteraction('serving_adjustment', {
            from: oldServings,
            to: newServings,
            timestamp: Date.now()
        });

        // Notification intelligente
        const message = newServings > oldServings ? 
            `🔼 Increased to ${newServings} servings` : 
            `🔽 Decreased to ${newServings} servings`;
        showSmartNotification(message, 'success', 2000);

        console.log(`🔄 Servings adjusted: ${oldServings} → ${newServings}`);
    };

    function animateServingChange(from, to) {
        const servingElement = $('#current-servings-storytelling, #current-servings-enhanced');
        
        // Animation de compteur
        $({count: from}).animate({count: to}, {
            duration: 800,
            easing: 'easeOutBounce',
            step: function() {
                servingElement.text(Math.round(this.count));
            },
            complete: function() {
                servingElement.text(to);
                
                // Effet de pulsation
                servingElement.addClass('serving-updated');
                setTimeout(() => {
                    servingElement.removeClass('serving-updated');
                }, 1000);
            }
        });

        // Feedback visuel dans le message
        const feedbackElement = $('#serving-feedback');
        if (feedbackElement.length) {
            feedbackElement.addClass('show');
            setTimeout(() => {
                feedbackElement.removeClass('show');
            }, 2000);
        }
    }

    function updateIngredientQuantitiesAdvanced() {
        const ratio = quickyInteractions.currentServings / quickyInteractions.baseServings;

        $('.ingredient-text-enhanced, .ingredient-name-story').each(function(index) {
            const originalIngredient = quickyInteractions.originalIngredients[index];
            if (originalIngredient) {
                const $this = $(this);
                const newText = recalculateIngredientAdvanced(originalIngredient, ratio);
                
                // Animation fluide de changement de texte
                $this.closest('.ingredient-card-storytelling, .ingredient-item-enhanced')
                     .addClass('ingredient-updating');
                
                $this.fadeOut(200, function() {
                    $this.text(newText.text).fadeIn(200);
                });
                
                setTimeout(() => {
                    $this.closest('.ingredient-card-storytelling, .ingredient-item-enhanced')
                         .removeClass('ingredient-updating');
                }, 800);
            }
        });
    }

    function parseIngredientText(text) {
        // Parser intelligent pour extraire quantité, unité et ingrédient
        const regex = /^(\d+(?:[.,/]\d+)?)\s*([a-zA-Z]*)\s+(.+)$/;
        const match = text.match(regex);
        
        if (match) {
            return {
                quantity: parseFloat(match[1].replace(',', '.')),
                unit: match[2] || '',
                ingredient: match[3] || text,
                originalText: text
            };
        }
        
        return {
            quantity: null,
            unit: '',
            ingredient: text,
            originalText: text
        };
    }

    function recalculateIngredientAdvanced(originalIngredient, ratio) {
        if (!originalIngredient.quantity) {
            return { text: originalIngredient.originalText, changed: false };
        }

        let newQuantity = originalIngredient.quantity * ratio;
        
        // Arrondir intelligemment selon la quantité
        if (newQuantity < 0.125) {
            newQuantity = Math.round(newQuantity * 32) / 32; // Au 1/32
        } else if (newQuantity < 0.5) {
            newQuantity = Math.round(newQuantity * 16) / 16; // Au 1/16
        } else if (newQuantity < 1) {
            newQuantity = Math.round(newQuantity * 8) / 8; // Au 1/8
        } else if (newQuantity < 10) {
            newQuantity = Math.round(newQuantity * 4) / 4; // Au 1/4
        } else {
            newQuantity = Math.round(newQuantity * 2) / 2; // Au 1/2
        }

        // Formater le nombre avec fractions
        const formattedQuantity = formatQuantityWithFractions(newQuantity);
        
        return {
            text: `${formattedQuantity} ${originalIngredient.unit} ${originalIngredient.ingredient}`,
            changed: newQuantity !== originalIngredient.quantity
        };
    }

    function formatQuantityWithFractions(quantity) {
        // Convertir en fractions communes pour de meilleurs résultats
        const fractions = {
            0.125: '1/8', 0.25: '1/4', 0.333: '1/3', 0.375: '3/8',
            0.5: '1/2', 0.625: '5/8', 0.666: '2/3', 0.75: '3/4', 0.875: '7/8'
        };
        
        const wholePart = Math.floor(quantity);
        const fractionalPart = quantity - wholePart;
        
        // Chercher la fraction la plus proche
        let closestFraction = '';
        let minDifference = Infinity;
        
        for (const [decimal, fraction] of Object.entries(fractions)) {
            const difference = Math.abs(parseFloat(decimal) - fractionalPart);
            if (difference < minDifference && difference < 0.05) {
                minDifference = difference;
                closestFraction = fraction;
            }
        }
        
        if (wholePart === 0) {
            return closestFraction || quantity.toString();
        } else if (closestFraction) {
            return `${wholePart} ${closestFraction}`;
        } else {
            return quantity % 1 === 0 ? quantity.toString() : quantity.toFixed(1);
        }
    }

    /**
     * INTERACTIONS INGRÉDIENTS AVANCÉES
     */
    function initializeIngredientInteractions() {
        // Restore état des ingrédients depuis localStorage
        restoreIngredientState();
        
        // Initialiser les interactions tactiles pour mobile
        initializeTouchInteractions();
        
        console.log('✅ Advanced ingredient interactions initialized');
    }

    window.toggleIngredientStory = window.toggleIngredientEnhanced = function(element) {
        const $element = $(element);
        const $card = $element.closest('.ingredient-card-storytelling, .ingredient-item-enhanced');
        const isChecked = $card.hasClass('checked');
        const ingredientIndex = $card.data('ingredient-index') || $card.index();
        
        if (isChecked) {
            // Décocher avec animation
            $card.removeClass('checked');
            createUndoAnimation($card);
            playInteractionSound('uncheck');
        } else {
            // Cocher avec animation de réussite
            $card.addClass('checked ingredient-checking');
            createSuccessAnimation($card);
            playInteractionSound('check');
            
            // Animation de confetti
            createIngredientConfetti($card);
            
            setTimeout(() => {
                $card.removeClass('ingredient-checking');
            }, 600);
        }

        // Haptic feedback
        triggerHapticFeedback(isChecked ? 'light' : 'medium');

        // Sauvegarder l'état
        saveIngredientState();
        
        // Analytics
        trackInteraction('ingredient_toggle', {
            index: ingredientIndex,
            checked: !isChecked,
            timestamp: Date.now()
        });

        // Vérifier si tous les ingrédients sont cochés
        checkAllIngredientsCompleted();

        console.log(`🥕 Ingredient ${ingredientIndex} ${!isChecked ? 'checked' : 'unchecked'}`);
    };

    function createSuccessAnimation(element) {
        const $success = $('<div class="success-animation">✓</div>');
        $success.css({
            position: 'absolute',
            top: '50%',
            left: '50%',
            transform: 'translate(-50%, -50%)',
            fontSize: '2rem',
            color: '#27ae60',
            fontWeight: 'bold',
            zIndex: 1000,
            pointerEvents: 'none'
        });
        
        element.css('position', 'relative').append($success);
        
        $success.animate({
            fontSize: '3rem',
            opacity: 0
        }, 600, function() {
            $success.remove();
        });
    }

    function createUndoAnimation(element) {
        const $undo = $('<div class="undo-animation">⟲</div>');
        $undo.css({
            position: 'absolute',
            top: '50%',
            right: '10px',
            transform: 'translateY(-50%)',
            fontSize: '1.5rem',
            color: '#e74c3c',
            fontWeight: 'bold',
            zIndex: 1000,
            pointerEvents: 'none'
        });
        
        element.css('position', 'relative').append($undo);
        
        $undo.animate({
            right: '30px',
            opacity: 0
        }, 400, function() {
            $undo.remove();
        });
    }

    function createIngredientConfetti(element) {
        const colors = ['#27ae60', '#3498db', '#e74c3c', '#f39c12', '#9b59b6'];
        const rect = element[0].getBoundingClientRect();
        
        for (let i = 0; i < 8; i++) {
            setTimeout(() => {
                const confetti = $('<div class="confetti-particle"></div>');
                confetti.css({
                    position: 'fixed',
                    left: rect.left + rect.width/2 + 'px',
                    top: rect.top + rect.height/2 + 'px',
                    width: '6px',
                    height: '6px',
                    backgroundColor: colors[Math.floor(Math.random() * colors.length)],
                    borderRadius: '50%',
                    pointerEvents: 'none',
                    zIndex: 10000
                });
                
                $('body').append(confetti);
                
                const angle = (Math.PI * 2 * i) / 8;
                const velocity = 50 + Math.random() * 30;
                let x = 0, y = 0, opacity = 1;
                
                const animate = () => {
                    x += Math.cos(angle) * velocity * 0.015;
                    y += Math.sin(angle) * velocity * 0.015 + 0.8;
                    opacity -= 0.02;
                    velocity *= 0.98;
                    
                    confetti.css({
                        transform: `translate(${x}px, ${y}px)`,
                        opacity: opacity
                    });
                    
                    if (opacity > 0) {
                        requestAnimationFrame(animate);
                    } else {
                        confetti.remove();
                    }
                };
                
                requestAnimationFrame(animate);
            }, i * 50);
        }
    }

    function checkAllIngredientsCompleted() {
        const totalIngredients = $('.ingredient-card-storytelling, .ingredient-item-enhanced').length;
        const checkedIngredients = $('.ingredient-card-storytelling.checked, .ingredient-item-enhanced.checked').length;
        
        if (totalIngredients > 0 && checkedIngredients === totalIngredients) {
            setTimeout(() => {
                showCelebrationModal('ingredients', 'Amazing! All ingredients ready! 🎉');
                triggerHapticFeedback('heavy');
                playInteractionSound('celebration');
            }, 500);
        }
    }

    /**
     * SUIVI DES INSTRUCTIONS AVANCÉ
     */
    function initializeInstructionTracking() {
        quickyInteractions.totalSteps = $('.instruction-journey-step, .instruction-step-enhanced').length;
        
        // Restaurer l'état des étapes
        restoreStepsState();
        
        // Initialiser la navigation par étapes
        initializeStepNavigation();
        
        console.log(`📝 Instruction tracking initialized: ${quickyInteractions.totalSteps} steps`);
    }

    window.markStepCompleteStory = window.markStepCompleteEnhanced = function(button, stepIndex) {
        const $button = $(button);
        const $step = $button.closest('.instruction-journey-step, .instruction-step-enhanced');
        const isCompleted = $button.hasClass('completed');
        const actualStepIndex = stepIndex !== undefined ? stepIndex : $step.data('step') - 1;
        
        if (isCompleted) {
            // Démarquer l'étape
            $button.removeClass('completed');
            $step.removeClass('step-completed');
            $button.find('.complete-text').text('Done');
            
            // Retirer de la liste des étapes complétées
            const index = quickyInteractions.completedSteps.indexOf(actualStepIndex);
            if (index > -1) {
                quickyInteractions.completedSteps.splice(index, 1);
            }
            
            playInteractionSound('uncheck');
        } else {
            // Marquer comme terminé
            $button.addClass('completed');
            $step.addClass('step-completed step-celebration');
            $button.find('.complete-text').text('Done!');
            
            // Animation de célébration
            createStepCompletionAnimation($step);
            
            // Ajouter à la liste
            if (!quickyInteractions.completedSteps.includes(actualStepIndex)) {
                quickyInteractions.completedSteps.push(actualStepIndex);
            }
            
            // Sons et vibrations
            playInteractionSound('step-complete');
            triggerHapticFeedback('medium');
            
            // Scroll automatique vers la prochaine étape
            setTimeout(() => {
                scrollToNextStep(actualStepIndex);
            }, 800);
            
            // Vérifier si c'est la dernière étape
            if (quickyInteractions.completedSteps.length === quickyInteractions.totalSteps) {
                setTimeout(() => {
                    showRecipeCompletionCelebration();
                }, 1000);
            }
            
            setTimeout(() => {
                $step.removeClass('step-celebration');
            }, 1200);
        }

        // Mettre à jour le progrès
        updateInstructionProgress();
        
        // Sauvegarder l'état
        saveStepsState();
        
        // Analytics
        trackInteraction('step_completion', {
            stepIndex: actualStepIndex,
            completed: !isCompleted,
            totalSteps: quickyInteractions.totalSteps,
            timestamp: Date.now()
        });

        console.log(`✅ Step ${actualStepIndex + 1} ${!isCompleted ? 'completed' : 'uncompleted'}`);
    };

    function createStepCompletionAnimation(stepElement) {
        // Animation de pulsation de succès
        stepElement.find('.step-number-journey, .step-number').addClass('step-success-pulse');
        
        // Effet de particules
        createStepParticles(stepElement);
        
        setTimeout(() => {
            stepElement.find('.step-number-journey, .step-number').removeClass('step-success-pulse');
        }, 1200);
    }

    function createStepParticles(stepElement) {
        const particles = ['✨', '⭐', '💫', '🌟'];
        const rect = stepElement[0].getBoundingClientRect();
        
        for (let i = 0; i < 6; i++) {
            setTimeout(() => {
                const particle = $(`<div class="step-particle">${particles[Math.floor(Math.random() * particles.length)]}</div>`);
                particle.css({
                    position: 'fixed',
                    left: rect.left + rect.width/2 + 'px',
                    top: rect.top + 50 + 'px',
                    fontSize: '20px',
                    pointerEvents: 'none',
                    zIndex: 10000
                });
                
                $('body').append(particle);
                
                const angle = (Math.PI * 2 * i) / 6;
                const distance = 100 + Math.random() * 50;
                
                particle.animate({
                    left: `+=${Math.cos(angle) * distance}px`,
                    top: `+=${Math.sin(angle) * distance - 30}px`,
                    fontSize: '12px',
                    opacity: 0
                }, 1500, function() {
                    particle.remove();
                });
            }, i * 100);
        }
    }

    function scrollToNextStep(currentStepIndex) {
        const nextStep = $(`.instruction-journey-step, .instruction-step-enhanced`).eq(currentStepIndex + 1);
        if (nextStep.length) {
            $('html, body').animate({
                scrollTop: nextStep.offset().top - 100
            }, 600, 'easeInOutCubic');
        }
    }

    function updateInstructionProgress() {
        const progressPercentage = quickyInteractions.totalSteps > 0 ? 
            (quickyInteractions.completedSteps.length / quickyInteractions.totalSteps) * 100 : 0;
        
        // Mettre à jour les barres de progrès
        $('.progress-fill-story, .progress-fill-enhanced, #instruction-progress-story, #instruction-progress-enhanced')
            .animate({width: progressPercentage + '%'}, 400);
        
        // Mettre à jour les compteurs
        $('#current-step-story, #current-step-enhanced').text(quickyInteractions.completedSteps.length);
        $('#total-steps-story, #total-steps-enhanced').text(quickyInteractions.totalSteps);
        
        // Animation de la barre si complète
        if (progressPercentage === 100) {
            $('.progress-fill-story, .progress-fill-enhanced').addClass('progress-complete');
        } else {
            $('.progress-fill-story, .progress-fill-enhanced').removeClass('progress-complete');
        }
    }

    /**
     * TIMERS INTELLIGENTS
     */
    function initializeSmartTimers() {
        // Initialiser les contrôles de timer
        initializeTimerControls();
        
        // Initialiser la notification des timers
        initializeTimerNotifications();
        
        console.log('⏲️ Smart timers initialized');
    }

    function initializeTimerControls() {
        // Gestionnaire pour les boutons de timer
        $(document).on('click', '.step-timer-btn-journey, .step-timer-btn-enhanced', function() {
            const $button = $(this);
            const $step = $button.closest('.instruction-journey-step, .instruction-step-enhanced');
            const stepIndex = $step.data('step') || $step.index();
            const timerText = $button.data('timer') || extractTimerFromStep($step);
            
            if (timerText) {
                const duration = parseTimerDuration(timerText);
                if (duration > 0) {
                    startSmartTimer(stepIndex, duration, timerText);
                    $button.addClass('timer-active');
                }
            }
        });
    }

    function extractTimerFromStep(stepElement) {
        // Extraire les durées du texte de l'étape
        const stepText = stepElement.text();
        const timeMatches = stepText.match(/(\d+)\s*(minutes?|mins?|seconds?|secs?|hours?|hrs?)/gi);
        
        if (timeMatches && timeMatches.length > 0) {
            return timeMatches[0];
        }
        
        return null;
    }

    function parseTimerDuration(timerText) {
        const match = timerText.match(/(\d+)\s*(minutes?|mins?|seconds?|secs?|hours?|hrs?)/i);
        
        if (match) {
            const value = parseInt(match[1]);
            const unit = match[2].toLowerCase();
            
            if (unit.startsWith('hour') || unit.startsWith('hr')) {
                return value * 3600; // heures en secondes
            } else if (unit.startsWith('minute') || unit.startsWith('min')) {
                return value * 60; // minutes en secondes
            } else if (unit.startsWith('second') || unit.startsWith('sec')) {
                return value; // déjà en secondes
            }
        }
        
        return 0;
    }

    function startSmartTimer(stepIndex, duration, label) {
        const timerId = `timer_${stepIndex}_${Date.now()}`;
        
        // Créer l'objet timer
        quickyInteractions.timers[timerId] = {
            stepIndex: stepIndex,
            duration: duration,
            remaining: duration,
            label: label,
            startTime: Date.now(),
            active: true,
            interval: null
        };
        
        // Créer l'interface du timer
        createTimerInterface(timerId);
        
        // Démarrer le compte à rebours
        startTimerCountdown(timerId);
        
        // Analytics
        trackInteraction('timer_start', {
            stepIndex: stepIndex,
            duration: duration,
            label: label,
            timestamp: Date.now()
        });
        
        console.log(`⏲️ Timer started for step ${stepIndex}: ${label} (${duration}s)`);
    }

    function createTimerInterface(timerId) {
        const timer = quickyInteractions.timers[timerId];
        const $timerElement = $(`
            <div class="smart-timer" id="${timerId}" data-step="${timer.stepIndex}">
                <div class="timer-header">
                    <span class="timer-icon">⏲️</span>
                    <span class="timer-label">${timer.label}</span>
                    <button class="timer-close" onclick="stopTimer('${timerId}')">×</button>
                </div>
                <div class="timer-display">
                    <div class="timer-time">${formatTimerDisplay(timer.remaining)}</div>
                    <div class="timer-progress">
                        <div class="timer-progress-bar" id="${timerId}-progress"></div>
                    </div>
                </div>
                <div class="timer-controls">
                    <button class="timer-btn pause" onclick="pauseTimer('${timerId}')">⏸️</button>
                    <button class="timer-btn reset" onclick="resetTimer('${timerId}')">🔄</button>
                    <button class="timer-btn add" onclick="addTimeToTimer('${timerId}', 60)">+1min</button>
                </div>
            </div>
        `);
        
        // Ajouter le timer à l'interface
        if ($('#active-timers').length === 0) {
            $('body').append('<div id="active-timers" class="active-timers-container"></div>');
        }
        
        $('#active-timers').append($timerElement);
        
        // Animation d'entrée
        $timerElement.hide().slideDown(300);
    }

    function startTimerCountdown(timerId) {
        const timer = quickyInteractions.timers[timerId];
        
        timer.interval = setInterval(() => {
            if (!timer.active) return;
            
            timer.remaining--;
            updateTimerDisplay(timerId);
            
            // Vérifier si le timer est terminé
            if (timer.remaining <= 0) {
                completeTimer(timerId);
            }
            
            // Notifications à intervalles spécifiques
            if (timer.remaining === 60) {
                showSmartNotification('⏰ 1 minute remaining!', 'warning', 3000);
                playInteractionSound('timer-warning');
            } else if (timer.remaining === 10) {
                showSmartNotification('⚠️ 10 seconds left!', 'error', 2000);
                playInteractionSound('timer-urgent');
            }
            
        }, 1000);
    }

    function updateTimerDisplay(timerId) {
        const timer = quickyInteractions.timers[timerId];
        const $timer = $(`#${timerId}`);
        
        // Mettre à jour l'affichage du temps
        $timer.find('.timer-time').text(formatTimerDisplay(timer.remaining));
        
        // Mettre à jour la barre de progrès
        const progressPercentage = ((timer.duration - timer.remaining) / timer.duration) * 100;
        $timer.find('.timer-progress-bar').css('width', progressPercentage + '%');
        
        // Changer la couleur selon le temps restant
        if (timer.remaining <= 10) {
            $timer.addClass('timer-urgent');
        } else if (timer.remaining <= 60) {
            $timer.addClass('timer-warning');
        }
    }

    function formatTimerDisplay(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }

    function completeTimer(timerId) {
        const timer = quickyInteractions.timers[timerId];
        
        // Arrêter l'intervalle
        clearInterval(timer.interval);
        timer.active = false;
        
        // Notification de fin
        showCelebrationModal('timer', `⏰ Timer completed: ${timer.label}!`);
        playInteractionSound('timer-complete');
        triggerHapticFeedback('heavy');
        
        // Animation de fin
        const $timer = $(`#${timerId}`);
        $timer.addClass('timer-completed');
        
        // Marquer automatiquement l'étape comme terminée si activé
        if (quickyInteractions.settings.autoProgress) {
            setTimeout(() => {
                const $step = $(`.instruction-journey-step, .instruction-step-enhanced`).eq(timer.stepIndex);
                const $completeBtn = $step.find('.step-complete-btn-journey, .step-complete-btn-enhanced');
                if ($completeBtn.length && !$completeBtn.hasClass('completed')) {
                    $completeBtn.trigger('click');
                }
            }, 2000);
        }
        
        // Analytics
        trackInteraction('timer_complete', {
            stepIndex: timer.stepIndex,
            duration: timer.duration,
            label: timer.label,
            timestamp: Date.now()
        });
        
        // Supprimer le timer après 5 secondes
        setTimeout(() => {
            $timer.slideUp(300, function() {
                $(this).remove();
            });
            delete quickyInteractions.timers[timerId];
        }, 5000);
        
        console.log(`✅ Timer completed: ${timer.label}`);
    }

    // Fonctions de contrôle des timers exposées globalement
    window.stopTimer = function(timerId) {
        const timer = quickyInteractions.timers[timerId];
        if (timer) {
            clearInterval(timer.interval);
            $(`#${timerId}`).slideUp(300, function() {
                $(this).remove();
            });
            delete quickyInteractions.timers[timerId];
        }
    };

    window.pauseTimer = function(timerId) {
        const timer = quickyInteractions.timers[timerId];
        if (timer) {
            timer.active = !timer.active;
            const $pauseBtn = $(`#${timerId} .timer-btn.pause`);
            $pauseBtn.text(timer.active ? '⏸️' : '▶️');
            $pauseBtn.toggleClass('paused', !timer.active);
        }
    };

    window.resetTimer = function(timerId) {
        const timer = quickyInteractions.timers[timerId];
        if (timer) {
            timer.remaining = timer.duration;
            timer.active = true;
            updateTimerDisplay(timerId);
            $(`#${timerId}`).removeClass('timer-warning timer-urgent');
        }
    };

    window.addTimeToTimer = function(timerId, seconds) {
        const timer = quickyInteractions.timers[timerId];
        if (timer) {
            timer.remaining += seconds;
            timer.duration += seconds;
            updateTimerDisplay(timerId);
            showSmartNotification(`⏲️ Added ${seconds}s to timer`, 'info', 2000);
        }
    };

    function initializeTimerNotifications() {
        // Demander les permissions de notification si pas encore accordées
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    }

    /**
     * SUIVI DU PROGRÈS AVANCÉ
     */
    function initializeProgressTracking() {
        // Initialiser le suivi du scroll pour la barre de lecture
        initializeScrollTracking();
        
        // Initialiser les badges de progression
        initializeProgressBadges();
        
        // Restaurer le progrès sauvegardé
        restoreProgressState();
        
        console.log('📊 Advanced progress tracking initialized');
    }

    function initializeScrollTracking() {
        let ticking = false;
        
        function updateScrollProgress() {
            const scrollTop = $(window).scrollTop();
            const docHeight = $(document).height();
            const winHeight = $(window).height();
            const scrollPercent = (scrollTop / (docHeight - winHeight)) * 100;
            
            // Mettre à jour la barre de progrès de lecture
            $('#reading-progress-storytelling, #reading-progress-enhanced, .progress-bar-storytelling, .progress-bar-professional')
                .css('width', Math.min(100, Math.max(0, scrollPercent)) + '%');
            
            // Analytics du scroll
            quickyInteractions.analytics.scrollDepth = Math.max(
                quickyInteractions.analytics.scrollDepth, 
                scrollPercent
            );
            
            ticking = false;
        }
        
        $(window).on('scroll', function() {
            if (!ticking) {
                requestAnimationFrame(updateScrollProgress);
                ticking = true;
            }
        });
    }

    function initializeProgressBadges() {
        // Créer les badges de progression
        const $progressBadges = $(`
            <div class="progress-badges-container">
                <div class="progress-badge ingredients-badge" id="ingredients-progress">
                    <span class="badge-icon">🥕</span>
                    <span class="badge-text">0%</span>
                    <span class="badge-label">Ingredients</span>
                </div>
                <div class="progress-badge steps-badge" id="steps-progress">
                    <span class="badge-icon">📝</span>
                    <span class="badge-text">0%</span>
                    <span class="badge-label">Steps</span>
                </div>
                <div class="progress-badge overall-badge" id="overall-progress">
                    <span class="badge-icon">🏆</span>
                    <span class="badge-text">0%</span>
                    <span class="badge-label">Overall</span>
                </div>
            </div>
        `);
        
        // Ajouter les badges si pas déjà présents
        if ($('.progress-badges-container').length === 0) {
            $('body').append($progressBadges);
        }
        
        // Mettre à jour les badges
        updateProgressBadges();
    }

    function updateProgressBadges() {
        // Calculer le progrès des ingrédients
        const totalIngredients = $('.ingredient-card-storytelling, .ingredient-item-enhanced').length;
        const checkedIngredients = $('.ingredient-card-storytelling.checked, .ingredient-item-enhanced.checked').length;
        const ingredientsProgress = totalIngredients > 0 ? Math.round((checkedIngredients / totalIngredients) * 100) : 0;
        
        // Calculer le progrès des étapes
        const stepsProgress = quickyInteractions.totalSteps > 0 ? 
            Math.round((quickyInteractions.completedSteps.length / quickyInteractions.totalSteps) * 100) : 0;
        
        // Calculer le progrès global
        const overallProgress = Math.round((ingredientsProgress + stepsProgress) / 2);
        
        // Mettre à jour les badges
        $('#ingredients-progress .badge-text').text(ingredientsProgress + '%');
        $('#steps-progress .badge-text').text(stepsProgress + '%');
        $('#overall-progress .badge-text').text(overallProgress + '%');
        
        // Ajouter des classes pour les niveaux de progression
        updateProgressBadgeClasses('#ingredients-progress', ingredientsProgress);
        updateProgressBadgeClasses('#steps-progress', stepsProgress);
        updateProgressBadgeClasses('#overall-progress', overallProgress);
        
        // Analytics
        quickyInteractions.analytics.ingredientsProgress = ingredientsProgress;
        quickyInteractions.analytics.stepsProgress = stepsProgress;
        quickyInteractions.analytics.overallProgress = overallProgress;
    }

    function updateProgressBadgeClasses(selector, progress) {
        const $badge = $(selector);
        $badge.removeClass('progress-low progress-medium progress-high progress-complete');
        
        if (progress === 100) {
            $badge.addClass('progress-complete');
        } else if (progress >= 75) {
            $badge.addClass('progress-high');
        } else if (progress >= 25) {
            $badge.addClass('progress-medium');
        } else {
            $badge.addClass('progress-low');
        }
    }

    /**
     * FONCTIONNALITÉS AVANCÉES
     */
    function initializeAdvancedFeatures() {
        // Initialiser toutes les fonctionnalités avancées
        initializeSmartSuggestions();
        initializeContentAdaptation();
        initializePerformanceOptimization();
        
        console.log('🚀 Advanced features initialized');
    }

    function initializeVoiceControl() {
        // Contrôle vocal (si supporté)
        if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            const recognition = new SpeechRecognition();
            
            recognition.continuous = false;
            recognition.interimResults = false;
            recognition.lang = 'fr-FR';
            
            recognition.onresult = function(event) {
                const command = event.results[0][0].transcript.toLowerCase();
                processVoiceCommand(command);
            };
            
            // Bouton d'activation vocale
            const $voiceBtn = $(`
                <button class="voice-control-btn" onclick="startVoiceRecognition()">
                    <span class="btn-icon">🎤</span>
                    <span class="btn-text">Voice Control</span>
                </button>
            `);
            
            $('.hero-actions-secondary, .floating-controls').append($voiceBtn);
            
            window.startVoiceRecognition = function() {
                recognition.start();
                showSmartNotification('🎤 Listening for commands...', 'info', 3000);
            };
            
            window.recognition = recognition;
            console.log('🎤 Voice control initialized');
        }
    }

    function processVoiceCommand(command) {
        console.log('Voice command received:', command);
        
        // Commandes pour les portions
        if (command.includes('augmente') || command.includes('plus')) {
            if (command.includes('portion')) {
                window.adjustServingsStory(1);
                showSmartNotification('🔊 Portions increased', 'success', 2000);
            }
        } else if (command.includes('diminue') || command.includes('moins')) {
            if (command.includes('portion')) {
                window.adjustServingsStory(-1);
                showSmartNotification('🔊 Portions decreased', 'success', 2000);
            }
        }
        
        // Commandes pour la navigation
        if (command.includes('suivant') || command.includes('next')) {
            scrollToNextStep(quickyInteractions.currentStep);
            showSmartNotification('🔊 Moving to next step', 'info', 2000);
        }
        
        // Commandes pour les timers
        if (command.includes('timer') || command.includes('minuteur')) {
            const timeMatch = command.match(/(\d+)\s*(minute|seconde)/);
            if (timeMatch) {
                const duration = parseInt(timeMatch[1]) * (timeMatch[2] === 'minute' ? 60 : 1);
                startSmartTimer('voice', duration, 'Voice Timer');
                showSmartNotification(`🔊 Timer set for ${timeMatch[1]} ${timeMatch[2]}(s)`, 'success', 2000);
            }
        }
    }

    function initializeGestureControls() {
        // Contrôles gestuels pour mobile
        let startX, startY, endX, endY;
        
        $(document).on('touchstart', function(e) {
            startX = e.originalEvent.touches[0].clientX;
            startY = e.originalEvent.touches[0].clientY;
        });
        
        $(document).on('touchend', function(e) {
            endX = e.originalEvent.changedTouches[0].clientX;
            endY = e.originalEvent.changedTouches[0].clientY;
            
            const deltaX = endX - startX;
            const deltaY = endY - startY;
            
            // Seuil minimum pour détecter un geste
            const threshold = 50;
            
            if (Math.abs(deltaX) > threshold || Math.abs(deltaY) > threshold) {
                if (Math.abs(deltaX) > Math.abs(deltaY)) {
                    // Geste horizontal
                    if (deltaX > 0) {
                        // Glissement vers la droite
                        handleSwipeRight();
                    } else {
                        // Glissement vers la gauche
                        handleSwipeLeft();
                    }
                } else {
                    // Geste vertical
                    if (deltaY > 0) {
                        // Glissement vers le bas
                        handleSwipeDown();
                    } else {
                        // Glissement vers le haut
                        handleSwipeUp();
                    }
                }
            }
        });
        
        console.log('👆 Gesture controls initialized');
    }

    function handleSwipeRight() {
        // Augmenter les portions
        window.adjustServingsStory(1);
    }

    function handleSwipeLeft() {
        // Diminuer les portions
        window.adjustServingsStory(-1);
    }

    function handleSwipeDown() {
        // Aller à l'étape suivante
        scrollToNextStep(quickyInteractions.currentStep);
    }

    function handleSwipeUp() {
        // Aller à l'étape précédente
        const prevStep = Math.max(0, quickyInteractions.currentStep - 1);
        const $prevStepElement = $(`.instruction-journey-step, .instruction-step-enhanced`).eq(prevStep);
        if ($prevStepElement.length) {
            $('html, body').animate({
                scrollTop: $prevStepElement.offset().top - 100
            }, 600);
        }
    }

    function initializeSmartNotifications() {
        // Créer le conteneur de notifications s'il n'existe pas
        if ($('#smart-notifications').length === 0) {
            $('body').append('<div id="smart-notifications" class="smart-notifications-container"></div>');
        }
        
        console.log('🔔 Smart notifications initialized');
    }

    function showSmartNotification(message, type = 'info', duration = 3000) {
        const notificationId = 'notification_' + Date.now();
        const typeIcons = {
            'success': '✅',
            'error': '❌',
            'warning': '⚠️',
            'info': 'ℹ️'
        };
        
        const $notification = $(`
            <div class="smart-notification ${type}" id="${notificationId}">
                <span class="notification-icon">${typeIcons[type] || 'ℹ️'}</span>
                <span class="notification-message">${message}</span>
                <button class="notification-close" onclick="closeNotification('${notificationId}')">×</button>
            </div>
        `);
        
        $('#smart-notifications').append($notification);
        
        // Animation d'entrée
        $notification.hide().slideDown(300);
        
        // Auto-suppression
        setTimeout(() => {
            closeNotification(notificationId);
        }, duration);
    }

    window.closeNotification = function(notificationId) {
        $(`#${notificationId}`).slideUp(300, function() {
            $(this).remove();
        });
    };

    function initializePersonalization() {
        // Charger les préférences utilisateur
        loadUserPreferences();
        
        // Créer l'interface de personnalisation
        createPersonalizationInterface();
        
        console.log('👤 Personalization initialized');
    }

    function loadUserPreferences() {
        const savedPrefs = localStorage.getItem('quicky_preferences');
        if (savedPrefs) {
            const prefs = JSON.parse(savedPrefs);
            Object.assign(quickyInteractions.settings, prefs);
            applyUserPreferences();
        }
    }

    function applyUserPreferences() {
        // Appliquer les préférences
        $('body').toggleClass('disable-animations', !quickyInteractions.settings.enableAnimations);
        $('body').toggleClass('disable-sounds', !quickyInteractions.settings.enableSounds);
        $('body').toggleClass('disable-haptics', !quickyInteractions.settings.enableHaptics);
        $('body').toggleClass('auto-progress', quickyInteractions.settings.autoProgress);
    }

    function saveUserPreferences() {
        localStorage.setItem('quicky_preferences', JSON.stringify(quickyInteractions.settings));
    }

    function createPersonalizationInterface() {
        const $settingsBtn = $(`
            <button class="settings-btn" onclick="toggleSettings()">
                <span class="btn-icon">⚙️</span>
            </button>
        `);
        
        const $settingsPanel = $(`
            <div class="settings-panel" id="settings-panel">
                <div class="settings-header">
                    <h3>⚙️ Settings</h3>
                    <button class="settings-close" onclick="toggleSettings()">×</button>
                </div>
                <div class="settings-content">
                    <div class="setting-item">
                        <label class="setting-label">
                            <input type="checkbox" id="enable-animations" ${quickyInteractions.settings.enableAnimations ? 'checked' : ''}>
                            <span class="checkmark"></span>
                            Enable Animations
                        </label>
                    </div>
                    <div class="setting-item">
                        <label class="setting-label">
                            <input type="checkbox" id="enable-sounds" ${quickyInteractions.settings.enableSounds ? 'checked' : ''}>
                            <span class="checkmark"></span>
                            Enable Sounds
                        </label>
                    </div>
                    <div class="setting-item">
                        <label class="setting-label">
                            <input type="checkbox" id="enable-haptics" ${quickyInteractions.settings.enableHaptics ? 'checked' : ''}>
                            <span class="checkmark"></span>
                            Enable Haptic Feedback
                        </label>
                    </div>
                    <div class="setting-item">
                        <label class="setting-label">
                            <input type="checkbox" id="auto-progress" ${quickyInteractions.settings.autoProgress ? 'checked' : ''}>
                            <span class="checkmark"></span>
                            Auto Progress on Timer Complete
                        </label>
                    </div>
                </div>
            </div>
        `);
        
        $('body').append($settingsBtn).append($settingsPanel);
        
        // Gestionnaires d'événements pour les paramètres
        $('#enable-animations').on('change', function() {
            quickyInteractions.settings.enableAnimations = this.checked;
            applyUserPreferences();
            saveUserPreferences();
        });
        
        $('#enable-sounds').on('change', function() {
            quickyInteractions.settings.enableSounds = this.checked;
            applyUserPreferences();
            saveUserPreferences();
        });
        
        $('#enable-haptics').on('change', function() {
            quickyInteractions.settings.enableHaptics = this.checked;
            applyUserPreferences();
            saveUserPreferences();
        });
        
        $('#auto-progress').on('change', function() {
            quickyInteractions.settings.autoProgress = this.checked;
            applyUserPreferences();
            saveUserPreferences();
        });
    }

    window.toggleSettings = function() {
        $('#settings-panel').toggleClass('open');
    };

    function initializeFloatingControls() {
        // Créer les contrôles flottants
        const $floatingControls = $(`
            <div class="floating-controls" id="floating-controls">
                <button class="floating-btn main-btn" onclick="toggleFloatingMenu()">
                    <span class="btn-icon">🎛️</span>
                </button>
                <div class="floating-menu" id="floating-menu">
                    <button class="floating-btn" onclick="resetAllProgress()" title="Reset Progress">
                        <span class="btn-icon">🔄</span>
                    </button>
                    <button class="floating-btn" onclick="exportProgress()" title="Export Progress">
                        <span class="btn-icon">📤</span>
                    </button>
                    <button class="floating-btn" onclick="shareRecipe()" title="Share Recipe">
                        <span class="btn-icon">📤</span>
                    </button>
                    <button class="floating-btn" onclick="printRecipe()" title="Print Recipe">
                        <span class="btn-icon">🖨️</span>
                    </button>
                </div>
            </div>
        `);
        
        $('body').append($floatingControls);
        
        console.log('🎛️ Floating controls initialized');
    }

    window.toggleFloatingMenu = function() {
        $('#floating-menu').toggleClass('open');
    };

    window.resetAllProgress = function() {
        if (confirm('Are you sure you want to reset all progress?')) {
            // Réinitialiser les ingrédients
            $('.ingredient-card-storytelling, .ingredient-item-enhanced').removeClass('checked');
            
            // Réinitialiser les étapes
            $('.step-complete-btn-journey, .step-complete-btn-enhanced').removeClass('completed');
            $('.instruction-journey-step, .instruction-step-enhanced').removeClass('step-completed');
            
            // Réinitialiser les données
            quickyInteractions.completedSteps = [];
            quickyInteractions.currentStep = 0;
            
            // Sauvegarder l'état
            saveIngredientState();
            saveStepsState();
            
            // Mettre à jour l'interface
            updateInstructionProgress();
            updateProgressBadges();
            
            showSmartNotification('🔄 Progress reset successfully', 'success', 2000);
        }
    };

    window.exportProgress = function() {
        const progressData = {
            completedSteps: quickyInteractions.completedSteps,
            currentServings: quickyInteractions.currentServings,
            checkedIngredients: $('.ingredient-card-storytelling.checked, .ingredient-item-enhanced.checked').map(function() {
                return $(this).index();
            }).get(),
            timestamp: new Date().toISOString(),
            analytics: quickyInteractions.analytics
        };
        
        const dataStr = JSON.stringify(progressData, null, 2);
        const dataBlob = new Blob([dataStr], { type: 'application/json' });
        const url = URL.createObjectURL(dataBlob);
        
        const link = document.createElement('a');
        link.href = url;
        link.download = 'recipe-progress.json';
        link.click();
        
        URL.revokeObjectURL(url);
        showSmartNotification('📤 Progress exported successfully', 'success', 2000);
    };

    window.shareRecipe = function() {
        if (navigator.share) {
            navigator.share({
                title: document.title,
                text: 'Check out this amazing recipe!',
                url: window.location.href
            });
        } else {
            // Fallback pour les navigateurs qui ne supportent pas l'API Web Share
            navigator.clipboard.writeText(window.location.href).then(() => {
                showSmartNotification('🔗 Recipe URL copied to clipboard', 'success', 2000);
            });
        }
    };

    window.printRecipe = function() {
        window.print();
    };

    function initializeThemeToggle() {
        // Détecter la préférence du système
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const savedTheme = localStorage.getItem('quicky_theme');
        const currentTheme = savedTheme || (prefersDark ? 'dark' : 'light');
        
        // Appliquer le thème
        $('body').attr('data-theme', currentTheme);
        
        // Créer le bouton de basculement
        const $themeToggle = $(`
            <button class="theme-toggle" onclick="toggleTheme()">
                <span class="theme-icon">${currentTheme === 'dark' ? '☀️' : '🌙'}</span>
            </button>
        `);
        
        $('.hero-actions-secondary, .floating-controls').append($themeToggle);
        
        console.log('🌙 Theme toggle initialized');
    }

    window.toggleTheme = function() {
        const currentTheme = $('body').attr('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        $('body').attr('data-theme', newTheme);
        $('.theme-icon').text(newTheme === 'dark' ? '☀️' : '🌙');
        
        localStorage.setItem('quicky_theme', newTheme);
        showSmartNotification(`🎨 Switched to ${newTheme} theme`, 'success', 2000);
    };

    function initializeAccessibilityFeatures() {
        // Raccourcis clavier
        $(document).on('keydown', function(e) {
            // Échap pour fermer les modales/panneaux
            if (e.key === 'Escape') {
                $('.modal, .settings-panel, #floating-menu').removeClass('open');
            }
        });
        
        // Navigation au clavier pour les ingrédients et étapes
        $('.ingredient-card-storytelling, .ingredient-item-enhanced').attr('tabindex', '0');
        $('.step-complete-btn-journey, .step-complete-btn-enhanced').attr('tabindex', '0');
        
        // Gérer l'activation avec la barre d'espace/entrée
        $(document).on('keydown', '.ingredient-card-storytelling, .ingredient-item-enhanced', function(e) {
            if (e.key === ' ' || e.key === 'Enter') {
                e.preventDefault();
                $(this).trigger('click');
            }
        });
        
        console.log('♿ Accessibility features initialized');
    }

    function initializeKeyboardShortcuts() {
        $(document).on('keydown', function(e) {
            // Raccourcis uniquement si aucun champ de saisie n'est actif
            if (!$(e.target).is('input, textarea, select')) {
                switch(e.key) {
                    case '=':
                    case '+':
                        e.preventDefault();
                        window.adjustServingsStory(1);
                        break;
                    case '-':
                        e.preventDefault();
                        window.adjustServingsStory(-1);
                        break;
                    case 'ArrowRight':
                        if (e.ctrlKey) {
                            e.preventDefault();
                            scrollToNextStep(quickyInteractions.currentStep);
                        }
                        break;
                    case 'ArrowLeft':
                        if (e.ctrlKey) {
                            e.preventDefault();
                            const prevStep = Math.max(0, quickyInteractions.currentStep - 1);
                            scrollToNextStep(prevStep - 1);
                        }
                        break;
                    case 'r':
                        if (e.ctrlKey && e.shiftKey) {
                            e.preventDefault();
                            window.resetAllProgress();
                        }
                        break;
                    case 's':
                        if (e.ctrlKey && e.shiftKey) {
                            e.preventDefault();
                            window.toggleSettings();
                        }
                        break;
                }
            }
        });
        
        console.log('⌨️ Keyboard shortcuts initialized');
    }

    /**
     * ANALYTICS ET SUIVI
     */
    function initializeAnalytics() {
        // Commencer le suivi du temps
        quickyInteractions.analytics.startTime = Date.now();
        
        // Suivi du temps passé sur la page
        setInterval(updateTimeOnPage, 1000);
        
        // Suivi des événements de départ
        $(window).on('beforeunload', function() {
            saveAnalyticsData();
        });
        
        console.log('📊 Analytics initialized');
    }

    function updateTimeOnPage() {
        quickyInteractions.analytics.timeOnPage = Date.now() - quickyInteractions.analytics.startTime;
    }

    function trackInteraction(type, data) {
        quickyInteractions.analytics.interactions.push({
            type: type,
            data: data,
            timestamp: Date.now()
        });
        
        // Limiter le nombre d'interactions stockées
        if (quickyInteractions.analytics.interactions.length > 100) {
            quickyInteractions.analytics.interactions.shift();
        }
    }

    function saveAnalyticsData() {
        // Sauvegarder les données analytiques
        localStorage.setItem('quicky_analytics', JSON.stringify(quickyInteractions.analytics));
    }

    /**
     * FONCTIONS UTILITAIRES
     */
    function triggerHapticFeedback(intensity = 'light') {
        if (!quickyInteractions.settings.enableHaptics) return;
        
        if ('vibrate' in navigator) {
            const patterns = {
                'light': [10],
                'medium': [20],
                'heavy': [50],
                'double': [20, 50, 20]
            };
            
            navigator.vibrate(patterns[intensity] || patterns.light);
        }
    }

    function playInteractionSound(type) {
        if (!quickyInteractions.settings.enableSounds) return;
        
        // Sons simples avec Web Audio API
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        const sounds = {
            'check': { frequency: 800, duration: 100 },
            'uncheck': { frequency: 400, duration: 100 },
            'adjustment': { frequency: 600, duration: 150 },
            'step-complete': { frequency: 1000, duration: 200 },
            'celebration': { frequency: 1200, duration: 300 },
            'timer-warning': { frequency: 700, duration: 500 },
            'timer-urgent': { frequency: 900, duration: 200 },
            'timer-complete': { frequency: 1100, duration: 400 }
        };
        
        const sound = sounds[type] || sounds.check;
        
        oscillator.frequency.setValueAtTime(sound.frequency, audioContext.currentTime);
        oscillator.type = 'sine';
        
        gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + sound.duration / 1000);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + sound.duration / 1000);
    }

    function showTemporaryMessage(message, type = 'info') {
        showSmartNotification(message, type, 2000);
    }

    function showCelebrationModal(celebrationType, message) {
        const $modal = $(`
            <div class="celebration-modal" id="celebration-modal">
                <div class="modal-content">
                    <div class="celebration-animation">
                        <div class="confetti-container" id="confetti-container"></div>
                        <div class="celebration-icon">${celebrationType === 'ingredients' ? '🎉' : celebrationType === 'timer' ? '⏰' : '🏆'}</div>
                        <h3 class="celebration-title">${message}</h3>
                        <button class="celebration-close" onclick="closeCelebrationModal()">Continue</button>
                    </div>
                </div>
            </div>
        `);
        
        $('body').append($modal);
        
        // Créer les confettis
        createCelebrationConfetti();
        
        // Auto-fermeture après 5 secondes
        setTimeout(() => {
            closeCelebrationModal();
        }, 5000);
    }

    window.closeCelebrationModal = function() {
        $('#celebration-modal').fadeOut(300, function() {
            $(this).remove();
        });
    };

    function createCelebrationConfetti() {
        const colors = ['#ff6b35', '#4ecdc4', '#ffe66d', '#27ae60', '#3498db', '#e74c3c', '#f39c12', '#9b59b6'];
        const confettiContainer = $('#confetti-container');
        
        for (let i = 0; i < 50; i++) {
            setTimeout(() => {
                const confetti = $('<div class="celebration-confetti"></div>');
                confetti.css({
                    position: 'absolute',
                    left: Math.random() * 100 + '%',
                    top: '-10px',
                    width: Math.random() * 10 + 5 + 'px',
                    height: Math.random() * 10 + 5 + 'px',
                    backgroundColor: colors[Math.floor(Math.random() * colors.length)],
                    borderRadius: Math.random() > 0.5 ? '50%' : '0',
                    pointerEvents: 'none',
                    zIndex: 10000
                });
                
                confettiContainer.append(confetti);
                
                // Animation de chute
                confetti.animate({
                    top: '120%',
                    left: '+=' + (Math.random() * 200 - 100) + 'px'
                }, Math.random() * 3000 + 2000, 'linear', function() {
                    $(this).remove();
                });
                
                // Rotation
                confetti.css('animation', `rotate 2s linear infinite`);
            }, i * 100);
        }
    }

    function showRecipeCompletionCelebration() {
        const $completionModal = $(`
            <div class="recipe-completion-modal" id="recipe-completion-modal">
                <div class="completion-content">
                    <div class="completion-header">
                        <div class="completion-icon">🎉</div>
                        <h2 class="completion-title">Recipe Completed!</h2>
                        <p class="completion-message">Congratulations! You've successfully completed this recipe. Time to enjoy your delicious creation!</p>
                    </div>
                    
                    <div class="completion-stats">
                        <div class="stat-item">
                            <span class="stat-icon">⏱️</span>
                            <span class="stat-label">Time Spent</span>
                            <span class="stat-value">${formatDuration(quickyInteractions.analytics.timeOnPage)}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-icon">📝</span>
                            <span class="stat-label">Steps Completed</span>
                            <span class="stat-value">${quickyInteractions.totalSteps}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-icon">🥕</span>
                            <span class="stat-label">Ingredients Used</span>
                            <span class="stat-value">${$('.ingredient-card-storytelling, .ingredient-item-enhanced').length}</span>
                        </div>
                    </div>
                    
                    <div class="completion-actions">
                        <button class="completion-btn primary" onclick="shareCompletion()">
                            <span class="btn-icon">📱</span>
                            Share Success
                        </button>
                        <button class="completion-btn secondary" onclick="rateRecipe()">
                            <span class="btn-icon">⭐</span>
                            Rate Recipe
                        </button>
                        <button class="completion-btn tertiary" onclick="closeCompletionModal()">
                            <span class="btn-icon">✓</span>
                            Continue
                        </button>
                    </div>
                </div>
            </div>
        `);
        
        $('body').append($completionModal);
        
        // Créer une célébration spéciale
        createMegaCelebration();
        
        // Analytics pour la completion
        trackInteraction('recipe_completed', {
            totalTime: quickyInteractions.analytics.timeOnPage,
            totalSteps: quickyInteractions.totalSteps,
            totalInteractions: quickyInteractions.analytics.interactions.length,
            timestamp: Date.now()
        });
    }

    function createMegaCelebration() {
        // Fireworks effect
        for (let i = 0; i < 5; i++) {
            setTimeout(() => {
                createFirework();
            }, i * 500);
        }
    }

    function createFirework() {
        const colors = ['#ff6b35', '#4ecdc4', '#ffe66d', '#27ae60', '#3498db', '#e74c3c'];
        const centerX = Math.random() * window.innerWidth;
        const centerY = Math.random() * (window.innerHeight * 0.5) + window.innerHeight * 0.2;
        
        for (let i = 0; i < 12; i++) {
            const spark = $('<div class="firework-spark"></div>');
            spark.css({
                position: 'fixed',
                left: centerX + 'px',
                top: centerY + 'px',
                width: '4px',
                height: '4px',
                backgroundColor: colors[Math.floor(Math.random() * colors.length)],
                borderRadius: '50%',
                pointerEvents: 'none',
                zIndex: 10001
            });
            
            $('body').append(spark);
            
            const angle = (Math.PI * 2 * i) / 12;
            const distance = 100 + Math.random() * 50;
            
            spark.animate({
                left: centerX + Math.cos(angle) * distance + 'px',
                top: centerY + Math.sin(angle) * distance + 'px',
                opacity: 0
            }, 1000, function() {
                $(this).remove();
            });
        }
    }

    function formatDuration(milliseconds) {
        const minutes = Math.floor(milliseconds / 60000);
        const seconds = Math.floor((milliseconds % 60000) / 1000);
        
        if (minutes > 0) {
            return `${minutes}m ${seconds}s`;
        } else {
            return `${seconds}s`;
        }
    }

    window.shareCompletion = function() {
        const shareText = `Just completed an amazing recipe! 🍳 Took me ${formatDuration(quickyInteractions.analytics.timeOnPage)} to make.`;
        
        if (navigator.share) {
            navigator.share({
                title: 'Recipe Completed!',
                text: shareText,
                url: window.location.href
            });
        } else {
            navigator.clipboard.writeText(shareText + ' ' + window.location.href).then(() => {
                showSmartNotification('📱 Success shared to clipboard!', 'success', 2000);
            });
        }
    };

    window.rateRecipe = function() {
        const $ratingModal = $(`
            <div class="rating-modal" id="rating-modal">
                <div class="rating-content">
                    <h3>⭐ Rate This Recipe</h3>
                    <p>How was your cooking experience?</p>
                    <div class="star-rating" id="star-rating">
                        <span class="star" data-rating="1">⭐</span>
                        <span class="star" data-rating="2">⭐</span>
                        <span class="star" data-rating="3">⭐</span>
                        <span class="star" data-rating="4">⭐</span>
                        <span class="star" data-rating="5">⭐</span>
                    </div>
                    <textarea class="rating-comment" placeholder="Share your thoughts (optional)..."></textarea>
                    <div class="rating-actions">
                        <button class="rating-btn primary" onclick="submitRating()">Submit Rating</button>
                        <button class="rating-btn secondary" onclick="closeRatingModal()">Skip</button>
                    </div>
                </div>
            </div>
        `);
        
        $('body').append($ratingModal);
        
        // Gestionnaire pour les étoiles
        $('.star').on('click', function() {
            const rating = $(this).data('rating');
            $('.star').removeClass('active');
            for (let i = 1; i <= rating; i++) {
                $(`.star[data-rating="${i}"]`).addClass('active');
            }
            $('#star-rating').data('rating', rating);
        });
    };

    window.submitRating = function() {
        const rating = $('#star-rating').data('rating') || 0;
        const comment = $('.rating-comment').val();
        
        if (rating === 0) {
            showSmartNotification('Please select a rating', 'warning', 2000);
            return;
        }
        
        // Sauvegarder la note (ici vous pourriez l'envoyer à votre serveur)
        const ratingData = {
            rating: rating,
            comment: comment,
            timestamp: new Date().toISOString(),
            recipeUrl: window.location.href
        };
        
        localStorage.setItem('quicky_rating_' + Date.now(), JSON.stringify(ratingData));
        
        showSmartNotification('⭐ Thank you for your rating!', 'success', 2000);
        closeRatingModal();
        closeCompletionModal();
    };

    window.closeRatingModal = function() {
        $('#rating-modal').fadeOut(300, function() {
            $(this).remove();
        });
    };

    window.closeCompletionModal = function() {
        $('#recipe-completion-modal').fadeOut(300, function() {
            $(this).remove();
        });
    };

    /**
     * FONCTIONS DE SAUVEGARDE ET RESTAURATION D'ÉTAT
     */
    function saveIngredientState() {
        const checkedIngredients = $('.ingredient-card-storytelling.checked, .ingredient-item-enhanced.checked')
            .map(function() { return $(this).index(); }).get();
        
        localStorage.setItem('quicky_ingredients_' + window.location.pathname, JSON.stringify(checkedIngredients));
    }

    function restoreIngredientState() {
        const savedState = localStorage.getItem('quicky_ingredients_' + window.location.pathname);
        if (savedState) {
            const checkedIndices = JSON.parse(savedState);
            checkedIndices.forEach(index => {
                $('.ingredient-card-storytelling, .ingredient-item-enhanced').eq(index).addClass('checked');
            });
        }
    }

    function saveStepsState() {
        const stepData = {
            completedSteps: quickyInteractions.completedSteps,
            currentStep: quickyInteractions.currentStep
        };
        
        localStorage.setItem('quicky_steps_' + window.location.pathname, JSON.stringify(stepData));
    }

    function restoreStepsState() {
        const savedState = localStorage.getItem('quicky_steps_' + window.location.pathname);
        if (savedState) {
            const stepData = JSON.parse(savedState);
            quickyInteractions.completedSteps = stepData.completedSteps || [];
            quickyInteractions.currentStep = stepData.currentStep || 0;
            
            // Restaurer l'état visuel des étapes
            quickyInteractions.completedSteps.forEach(stepIndex => {
                const $step = $(`.instruction-journey-step, .instruction-step-enhanced`).eq(stepIndex);
                const $button = $step.find('.step-complete-btn-journey, .step-complete-btn-enhanced');
                $button.addClass('completed');
                $step.addClass('step-completed');
                $button.find('.complete-text').text('Done!');
            });
            
            updateInstructionProgress();
        }
    }

    function restoreProgressState() {
        // Restaurer l'état général du progrès
        updateProgressBadges();
        
        // Vérifier si la recette était complétée
        if (quickyInteractions.completedSteps.length === quickyInteractions.totalSteps && 
            quickyInteractions.totalSteps > 0) {
            
            setTimeout(() => {
                showSmartNotification('🎉 Welcome back! Your recipe is completed.', 'success', 3000);
            }, 1000);
        }
    }

    function initializeTouchInteractions() {
        // Support des interactions tactiles pour mobile
        let touchStartTime;
        let touchStartPos;
        
        $(document).on('touchstart', '.ingredient-card-storytelling, .ingredient-item-enhanced', function(e) {
            touchStartTime = Date.now();
            touchStartPos = {
                x: e.originalEvent.touches[0].clientX,
                y: e.originalEvent.touches[0].clientY
            };
        });
        
        $(document).on('touchend', '.ingredient-card-storytelling, .ingredient-item-enhanced', function(e) {
            const touchEndTime = Date.now();
            const touchDuration = touchEndTime - touchStartTime;
            
            // Long press pour afficher les détails
            if (touchDuration > 500) {
                e.preventDefault();
                const $card = $(this);
                $card.find('.ingredient-details-story').slideToggle(300);
                triggerHapticFeedback('medium');
            }
        });
        
        console.log('👆 Touch interactions initialized');
    }

    function initializeStepNavigation() {
        // Navigation intelligente entre les étapes
        $(document).on('click', '.step-number-journey, .step-number', function() {
            const $step = $(this).closest('.instruction-journey-step, .instruction-step-enhanced');
            const stepIndex = $step.data('step') - 1 || $step.index();
            
            // Scroll vers l'étape
            $('html, body').animate({
                scrollTop: $step.offset().top - 100
            }, 600);
            
            quickyInteractions.currentStep = stepIndex;
            
            // Mettre en surbrillance l'étape active
            $('.instruction-journey-step, .instruction-step-enhanced').removeClass('active-step');
            $step.addClass('active-step');
            
            trackInteraction('step_navigation', {
                stepIndex: stepIndex,
                timestamp: Date.now()
            });
        });
    }

    /**
     * FONCTIONNALITÉS AVANCÉES SUPPLÉMENTAIRES
     */
    function initializeSmartSuggestions() {
        // Suggestions intelligentes basées sur le comportement
        let suggestionTimeout;
        
        // Observer l'inactivité pour proposer de l'aide
        $(document).on('mousemove keypress scroll touchstart', function() {
            clearTimeout(suggestionTimeout);
            
            suggestionTimeout = setTimeout(() => {
                showInactivitySuggestion();
            }, 60000); // 1 minute d'inactivité
        });
        
        console.log('💡 Smart suggestions initialized');
    }

    function showInactivitySuggestion() {
        // Analyser le progrès actuel et proposer une aide contextuelle
        const ingredientsProgress = $('.ingredient-card-storytelling.checked, .ingredient-item-enhanced.checked').length;
        const totalIngredients = $('.ingredient-card-storytelling, .ingredient-item-enhanced').length;
        const stepsProgress = quickyInteractions.completedSteps.length;
        
        let suggestion = '';
        
        if (ingredientsProgress === 0 && totalIngredients > 0) {
            suggestion = '💡 Tip: Start by checking off ingredients as you gather them!';
        } else if (ingredientsProgress === totalIngredients && stepsProgress === 0) {
            suggestion = '🍳 Ready to cook? Start with the first step below!';
        } else if (stepsProgress > 0 && stepsProgress < quickyInteractions.totalSteps) {
            suggestion = '⏱️ Need a timer? Click the timer button next to cooking steps!';
        }
        
        if (suggestion) {
            showSmartNotification(suggestion, 'info', 5000);
        }
    }

    function initializeContentAdaptation() {
        // Adaptation du contenu basée sur l'appareil et les préférences
        const isMobile = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        const isTouch = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
        
        if (isMobile || isTouch) {
            $('body').addClass('mobile-device touch-device');
            
            // Ajuster les tailles des éléments tactiles
            $('.step-complete-btn-journey, .step-complete-btn-enhanced, .ingredient-checkbox-storytelling')
                .css('min-height', '44px').css('min-width', '44px');
        }
        
        // Adaptation à la connexion réseau
        if ('connection' in navigator) {
            const connection = navigator.connection;
            if (connection.effectiveType === 'slow-2g' || connection.effectiveType === '2g') {
                $('body').addClass('slow-connection');
                // Désactiver les animations coûteuses
                quickyInteractions.settings.enableAnimations = false;
                applyUserPreferences();
            }
        }
        
        console.log('📱 Content adaptation initialized');
    }

    function initializePerformanceOptimization() {
        // Optimisations de performance
        
        // Lazy loading des images
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                            imageObserver.unobserve(img);
                        }
                    }
                });
            });
            
            $('img[data-src]').each(function() {
                imageObserver.observe(this);
            });
        }
        
        // Throttling des événements scroll et resize
        let scrollTimeout;
        let resizeTimeout;
        
        $(window).on('scroll', function() {
            if (!scrollTimeout) {
                scrollTimeout = setTimeout(() => {
                    scrollTimeout = null;
                    // Actions de scroll throttlées
                    updateActiveStep();
                }, 16); // 60fps
            }
        });
        
        $(window).on('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                adjustLayoutForScreenSize();
            }, 250);
        });
        
        console.log('⚡ Performance optimization initialized');
    }

    function updateActiveStep() {
        // Détecter quelle étape est actuellement visible
        const scrollTop = $(window).scrollTop();
        const windowHeight = $(window).height();
        const centerViewport = scrollTop + windowHeight / 2;
        
        $('.instruction-journey-step, .instruction-step-enhanced').each(function(index) {
            const $step = $(this);
            const stepTop = $step.offset().top;
            const stepBottom = stepTop + $step.outerHeight();
            
            if (centerViewport >= stepTop && centerViewport <= stepBottom) {
                if (quickyInteractions.currentStep !== index) {
                    quickyInteractions.currentStep = index;
                    
                    // Mettre à jour l'indicateur d'étape active
                    $('.instruction-journey-step, .instruction-step-enhanced').removeClass('in-viewport');
                    $step.addClass('in-viewport');
                }
            }
        });
    }

    function adjustLayoutForScreenSize() {
        // Ajuster la mise en page selon la taille d'écran
        const screenWidth = $(window).width();
        
        if (screenWidth < 768) {
            $('body').addClass('small-screen');
            // Réduire les animations sur petit écran
            $('.floating-controls').addClass('compact');
        } else {
            $('body').removeClass('small-screen');
            $('.floating-controls').removeClass('compact');
        }
    }

    /**
     * EXPORT ET INTÉGRATION
     */
    
    // Exposer les fonctions principales pour usage externe
    window.QuickyInteractions = {
        // API publique
        adjustServings: window.adjustServingsStory,
        toggleIngredient: window.toggleIngredientStory,
        markStepComplete: window.markStepCompleteStory,
        startTimer: startSmartTimer,
        showNotification: showSmartNotification,
        
        // Données
        getProgress: function() {
            return {
                completedSteps: quickyInteractions.completedSteps,
                currentServings: quickyInteractions.currentServings,
                analytics: quickyInteractions.analytics,
                timers: Object.keys(quickyInteractions.timers).length
            };
        },
        
        // Configuration
        updateSettings: function(newSettings) {
            Object.assign(quickyInteractions.settings, newSettings);
            applyUserPreferences();
            saveUserPreferences();
        },
        
        getSettings: function() {
            return quickyInteractions.settings;
        },
        
        // Événements personnalisés
        on: function(event, callback) {
            $(document).on('quicky.' + event, callback);
        },
        
        trigger: function(event, data) {
            $(document).trigger('quicky.' + event, data);
        }
    };

    // Événements personnalisés pour l'intégration
    function triggerCustomEvent(eventName, data) {
        $(document).trigger('quicky.' + eventName, data);
    }

    // Déclencher des événements lors des actions importantes
    const originalToggleIngredient = window.toggleIngredientStory;
    window.toggleIngredientStory = function(element) {
        const result = originalToggleIngredient(element);
        const $card = $(element).closest('.ingredient-card-storytelling, .ingredient-item-enhanced');
        const isChecked = $card.hasClass('checked');
        
        triggerCustomEvent('ingredient.toggled', {
            element: element,
            checked: isChecked,
            index: $card.index()
        });
        
        return result;
    };

    const originalMarkStepComplete = window.markStepCompleteStory;
    window.markStepCompleteStory = function(button, stepIndex) {
        const result = originalMarkStepComplete(button, stepIndex);
        const $button = $(button);
        const isCompleted = $button.hasClass('completed');
        
        triggerCustomEvent('step.completed', {
            button: button,
            stepIndex: stepIndex,
            completed: isCompleted
        });
        
        return result;
    };

    // Debug et développement
    if (window.location.hostname === 'localhost' || window.location.search.includes('debug=true')) {
        window.QuickyDebug = {
            interactions: quickyInteractions,
            simulateCompletion: function() {
                // Simuler la completion pour les tests
                $('.ingredient-card-storytelling, .ingredient-item-enhanced').addClass('checked');
                $('.step-complete-btn-journey, .step-complete-btn-enhanced').addClass('completed');
                quickyInteractions.completedSteps = Array.from({length: quickyInteractions.totalSteps}, (_, i) => i);
                updateInstructionProgress();
                updateProgressBadges();
                showRecipeCompletionCelebration();
            },
            resetAll: function() {
                window.resetAllProgress();
            },
            showAnalytics: function() {
                console.table(quickyInteractions.analytics);
            }
        };
        
        console.log('🐛 Debug mode enabled. Use window.QuickyDebug for testing.');
    }

    // Message de fin d'initialisation
    console.log('🎉 Quicky Interactions Pro v2.0 fully loaded and ready!');
    console.log('📊 Tracking:', Object.keys(quickyInteractions.analytics).length, 'analytics metrics');
    console.log('⚡ Features loaded:', {
        servingsAdjustment: true,
        ingredientTracking: true,
        stepCompletion: true,
        smartTimers: true,
        voiceControl: 'webkitSpeechRecognition' in window,
        hapticFeedback: 'vibrate' in navigator,
        notifications: 'Notification' in window
    });

})(jQuery);

// CSS animations et styles additionnels injectés dynamiquement
(function() {
    const additionalStyles = `
        <style id="quicky-interactions-styles">
        /* Animations de rotation pour les confettis */
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        /* Styles pour les timers */
        .smart-timer {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-left: 4px solid var(--quicky-primary, #ff6b35);
            transition: all 0.3s ease;
        }
        
        .smart-timer.timer-warning {
            border-left-color: #f39c12;
            background: linear-gradient(135deg, #fff9e6, #ffffff);
        }
        
        .smart-timer.timer-urgent {
            border-left-color: #e74c3c;
            background: linear-gradient(135deg, #ffebee, #ffffff);
            animation: pulse 1s infinite;
        }
        
        .smart-timer.timer-completed {
            border-left-color: #27ae60;
            background: linear-gradient(135deg, #e8f5e8, #ffffff);
        }
        
        .timer-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        
        .timer-display {
            text-align: center;
            margin: 1rem 0;
        }
        
        .timer-time {
            font-size: 2rem;
            font-weight: bold;
            font-family: monospace;
            color: var(--quicky-dark, #2c3e50);
        }
        
        .timer-progress {
            width: 100%;
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            margin-top: 0.5rem;
            overflow: hidden;
        }
        
        .timer-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--quicky-primary, #ff6b35), var(--quicky-secondary, #4ecdc4));
            width: 0%;
            transition: width 1s ease;
        }
        
        .timer-controls {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }
        
        .timer-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .timer-btn:hover {
            background: #e9ecef;
            transform: translateY(-1px);
        }
        
        .timer-btn.paused {
            background: #ffc107;
            color: white;
        }
        
        /* Styles pour les badges de progression */
        .progress-badges-container {
            position: fixed;
            bottom: 20px;
            left: 20px;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            z-index: 1000;
        }
        
        .progress-badge {
            background: white;
            border-radius: 8px;
            padding: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            min-width: 120px;
            transition: all 0.3s ease;
        }
        
        .progress-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        
        .progress-badge.progress-complete {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
        }
        
        .progress-badge.progress-high {
            background: linear-gradient(135deg, #3498db, #5dade2);
            color: white;
        }
        
        .progress-badge.progress-medium {
            background: linear-gradient(135deg, #f39c12, #f7dc6f);
            color: white;
        }
        
        .progress-badge.progress-low {
            background: linear-gradient(135deg, #e74c3c, #ec7063);
            color: white;
        }
        
        .badge-text {
            font-weight: bold;
            font-size: 0.9rem;
        }
        
        .badge-label {
            font-size: 0.7rem;
            opacity: 0.8;
        }
        
        /* Styles pour les modales */
        .celebration-modal,
        .recipe-completion-modal,
        .rating-modal {
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
            backdrop-filter: blur(5px);
        }
        
        .modal-content,
        .completion-content,
        .rating-content {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            max-width: 500px;
            width: 90%;
            text-align: center;
            position: relative;
            animation: modalSlideIn 0.3s ease;
        }
        
        @keyframes modalSlideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .celebration-icon,
        .completion-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .completion-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-icon {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 0.8rem;
            color: #666;
            margin-bottom: 0.25rem;
        }
        
        .stat-value {
            font-weight: bold;
            color: var(--quicky-primary, #ff6b35);
        }
        
        /* Rating stars */
        .star-rating {
            margin: 1rem 0;
        }
        
        .star {
            font-size: 2rem;
            cursor: pointer;
            opacity: 0.3;
            transition: all 0.2s ease;
        }
        
        .star:hover,
        .star.active {
            opacity: 1;
            transform: scale(1.1);
        }
        
        .rating-comment {
            width: 100%;
            min-height: 80px;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 0.5rem;
            margin: 1rem 0;
            font-family: inherit;
            resize: vertical;
        }
        
        /* Styles pour les contrôles flottants */
        .floating-controls {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .floating-btn {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            border: none;
            background: var(--quicky-primary, #ff6b35);
            color: white;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5rem;
        }
        
        .floating-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }
        
        .floating-menu {
            display: none;
        }
        
        .floating-menu.open {
            display: block;
        }
        
        .floating-menu .floating-btn {
            opacity: 0;
            transform: scale(0);
            animation: floatingBtnIn 0.2s ease forwards;
        }
        
        .floating-menu .floating-btn:nth-child(1) { animation-delay: 0.1s; }
        .floating-menu .floating-btn:nth-child(2) { animation-delay: 0.2s; }
        .floating-menu .floating-btn:nth-child(3) { animation-delay: 0.3s; }
        .floating-menu .floating-btn:nth-child(4) { animation-delay: 0.4s; }
        
        @keyframes floatingBtnIn {
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        /* Styles pour les notifications */
        .smart-notifications-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            max-width: 300px;
        }
        
        .smart-notification {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-left: 4px solid #007bff;
        }
        
        .smart-notification.success {
            border-left-color: #28a745;
        }
        
        .smart-notification.error {
            border-left-color: #dc3545;
        }
        
        .smart-notification.warning {
            border-left-color: #ffc107;
        }
        
        .notification-close {
            margin-left: auto;
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            opacity: 0.5;
        }
        
        .notification-close:hover {
            opacity: 1;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .progress-badges-container {
                bottom: 10px;
                left: 10px;
            }
            
            .floating-controls {
                bottom: 10px;
                right: 10px;
            }
            
            .floating-btn {
                width: 48px;
                height: 48px;
            }
            
            .smart-notifications-container {
                top: 10px;
                right: 10px;
                left: 10px;
                max-width: none;
            }
            
            .modal-content,
            .completion-content,
            .rating-content {
                margin: 1rem;
                width: calc(100% - 2rem);
            }
        }
        
        /* Animations de célébration */
        .step-success-pulse {
            animation: successPulse 1.2s ease;
        }
        
        @keyframes successPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); background: #27ae60; color: white; }
        }
        
        .serving-updated {
            animation: servingUpdate 1s ease;
        }
        
        @keyframes servingUpdate {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); color: var(--quicky-primary, #ff6b35); }
        }
        
        /* États des ingrédients */
        .ingredient-updating {
            animation: ingredientUpdate 0.8s ease;
        }
        
        @keyframes ingredientUpdate {
            0%, 100% { background: white; }
            50% { background: #e8f5e8; transform: scale(1.02); }
        }
        
        .ingredient-checking {
            animation: ingredientCheck 0.6s ease;
        }
        
        @keyframes ingredientCheck {
            0% { background: white; }
            50% { background: #e8f5e8; }
            100% { background: #d4edda; }
        }
        
        /* Étapes actives */
        .active-step {
            background: linear-gradient(135deg, rgba(255, 107, 53, 0.1), rgba(78, 205, 196, 0.1));
            border-left: 4px solid var(--quicky-primary, #ff6b35);
        }
        
        .in-viewport {
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.2);
        }
        
        .step-celebration {
            animation: stepCelebration 1.2s ease;
        }
        
        @keyframes stepCelebration {
            0%, 100% { transform: scale(1); }
            25% { transform: scale(1.02); }
            50% { transform: scale(1.05); background: #e8f5e8; }
            75% { transform: scale(1.02); }
        }
        
        /* Accessibilité */
        .focus-visible {
            outline: 2px solid var(--quicky-primary, #ff6b35);
            outline-offset: 2px;
        }
        
        /* Mode sombre */
        [data-theme="dark"] {
            --quicky-dark: #ecf0f1;
            --quicky-white: #2c3e50;
            --quicky-light: #34495e;
            --quicky-gray: #95a5a6;
        }
        
        [data-theme="dark"] .smart-timer,
        [data-theme="dark"] .progress-badge,
        [data-theme="dark"] .smart-notification,
        [data-theme="dark"] .modal-content,
        [data-theme="dark"] .completion-content,
        [data-theme="dark"] .rating-content {
            background: #34495e;
            color: #ecf0f1;
        }
        
        /* Réduction des animations pour les utilisateurs sensibles */
        @media (prefers-reduced-motion: reduce) {
            .disable-animations *,
            .disable-animations *::before,
            .disable-animations *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
        
        /* Connexions lentes */
        .slow-connection .smart-timer,
        .slow-connection .progress-badge {
            animation: none !important;
        }
        
        /* Petits écrans */
        .small-screen .floating-controls.compact .floating-btn {
            width: 40px;
            height: 40px;
        }
        
        .small-screen .floating-controls.compact .btn-icon {
            font-size: 0.9rem;
        }
        </style>
    `;
    
    $('head').append(additionalStyles);
})();