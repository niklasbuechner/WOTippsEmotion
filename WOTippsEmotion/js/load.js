$.overridePlugin('swEmotionLoader', {
    
        loadEmotion: function(controllerUrl, deviceState) {
            var me = this,
                devices = me.availableDevices,
                types = me.opts.deviceTypes,
                url = controllerUrl || me.opts.controllerUrl,
                state = deviceState || StateManager.getCurrentState();

            /**
             * If the emotion world is not defined for the current device,
             * hide the wrapper element and show the default content.
             */
            if (devices.indexOf(types[state]) === -1) {
                var hasSameDeviceSibling = false;

                me.hideEmotion();

                me.$siblings.each(function(index, el) {
                    var devices = $(el).attr('data-availabledevices');

                    if (devices.indexOf(types[state]) !== -1) {
                        hasSameDeviceSibling = true;
                    }
                });

                if (!hasSameDeviceSibling) me.showFallbackContent();
                return;
            }

            /**
             * If the plugin is not configured correctly show the default content.
             */
            if (!devices.length || !state.length || !url.length) {
                me.hideEmotion();
                me.showFallbackContent();
                return;
            }

            /**
             * If the emotion world was already loaded show it.
             */
            if (me.$emotion.length) {

                (me.opts.showListing) ? me.showFallbackContent() : me.hideFallbackContent();

                me.$overlay.remove();
                me.showEmotion();
                return;
            }

            /**
             * Show the loading indicator and load the emotion world.
             */
            me.showEmotion();

            if (me.isLoading) {
                return;
            }

            me.isLoading = true;
            me.$overlay.insertBefore('.content-main');
            
            //
            //
            // Change start
            //
            //
            
            var successFunc = function (response) {

                    me.isLoading = false;
                    me.$overlay.remove();

                    $.publish('plugin/swEmotionLoader/onLoadEmotionLoaded', [ me ]);

                    if (!response.length) {
                        me.hideEmotion();
                        me.showFallbackContent();
                        return;
                    }

                    (me.opts.showListing) ? me.showFallbackContent() : me.hideFallbackContent();

                    me.initEmotion(response);

                    $.publish('plugin/swEmotionLoader/onLoadEmotionFinished', [ me ]);
                }
            
            var cssClass = 'wotipps-' + url.replace(/\//g, '-');
            var contentEls = document.getElementsByClassName(cssClass)
            
            if (contentEls.length > 0)
            {
                //alert('content already exists');
                var emotion = contentEls[0].innerHTML;
                
                successFunc(emotion.replace(/wotipps-product-slider-hidden/g, ''));
                
                $.publish('plugin/swEmotionLoader/onLoadEmotion', [ me ]);
                
                return;
            }
            else
            {
                //alert('content does not yet exist');
                //alert(cssClass);
            }
            
            
            //
            //
            // Change end
            //
            //

            $.ajax({
                url: url,
                method: 'GET',
                success: successFunc
            });

            $.publish('plugin/swEmotionLoader/onLoadEmotion', [ me ]);
        }
});
$.overridePlugin('swProductSlider', {
        
    
    init: function () {
            
            var me = this;

            me.applyDataAttributes();

            me.autoScrollAnimation = false;
            me.autoSlideAnimation = false;
            me.bufferedCall = false;
            me.initialized = false;

            me.isLoading = false;
            me.isAnimating = false;
            
            if (me.$el.hasClass('wotipps-product-slider-hidden'))
            {
                // the emotion is embedded but its not displayed
                // therefore do not initialise it
                return;
            }

            if (me.opts.mode === 'ajax' && me.opts.ajaxCtrlUrl === null) {
                console.error('The controller url for the ajax slider is not defined!');
                return;
            }

            if (me.opts.mode === 'ajax' && me.opts.ajaxShowLoadingIndicator) {
                me.showLoadingIndicator();
            }

            if (me.opts.initOnEvent !== null) {
                $.subscribe(me.opts.initOnEvent, function() {
                    if (!me.initialized) {
                        me.initSlider();
                        me.registerEvents();
                    }
                });
            } else {
                me.initSlider();
                me.registerEvents();
            }
        },
        
        createArrows: function () {
            var me = this,
                orientationCls = (me.opts.orientation === 'vertical') ? me.opts.verticalCls : me.opts.horizontalCls;

            if (!me.opts.arrowControls || !me.isActive()) {
                return;
            }

            if (!me.$arrowPrev) {
                
                if (me.$el.find('.' + me.opts.prevArrowCls).length > 0)
                {
                    me.$el.find('.' + me.opts.prevArrowCls).remove();
                }
                
                me.$arrowPrev = $('<a>', {
                    'class': me.opts.arrowCls + ' ' +
                        me.opts.prevArrowCls + ' ' +
                        orientationCls
                }).prependTo(me.$el);

                me._on(me.$arrowPrev, 'click', $.proxy(me.onArrowClick, me, 'prev'));
            }

            if (!me.$arrowNext) {
                
                if (me.$el.find('.' + me.opts.nextArrowCls).length > 0)
                {
                    me.$el.find('.' + me.opts.nextArrowCls).remove();
                }
                
                me.$arrowNext = $('<a>', {
                    'class': me.opts.arrowCls + ' ' +
                        me.opts.nextArrowCls + ' ' +
                        orientationCls
                }).prependTo(me.$el);

                me._on(me.$arrowNext, 'click', $.proxy(me.onArrowClick, me, 'next'));
            }

            me.trackArrows();

            $.publish('plugin/swProductSlider/onCreateArrows', [ me, me.$arrowPrev, me.$arrowNext ]);
        },
});