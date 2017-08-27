{block name='frontend_index_header_javascript_jquery_lib' prepend}

<script type="text/javascript">
/* 
 * WOTipps
 * By Niklas Buechner
 * http://www.wotipps.de
 * This code may not be distributed without written permission.
 */
{*{literal}
(function(){var m;function q(e,a,f){a=a?a:e;f=a.getElementsByClassName(f?f:"banner--content")[0];var b=window.getComputedStyle(e);e=b.width;b=b.height;a=a.dataset.width/a.dataset.height;var g=a>e/b;f.style.width=g?b*a+"px":"100%";f.style.height=g?"100%":e/a+"px"}function n(){var e=window.innerWidth;m="number"!=typeof e?-1:480>e?{name:"xs",b:4}:768>e?{name:"s",b:3}:1024>e?{name:"m",b:2}:1260>e?{name:"l",b:1}:{name:"xl",b:0};var e=document.getElementsByClassName("emotion--wrapper"),n=e.length;0<document.getElementsByClassName("emotion--overlay").length&&
(document.getElementsByClassName("emotion--overlay")[0].outerHTML="");for(var f=0;f<n;f++)if(-1==e[f].dataset.availabledevices.indexOf(m.b))e[f].outerHTML="";else{a={};a.a=e[f];var b=a.a.getElementsByClassName("emotion--container")[0];a.width=a.a.offsetWidth;if("resize"==b.dataset.gridmode){var g=~~b.dataset.basewidth,d=~~b.dataset.cellspacing;a.c=g;b.style.width=g+d+"px";g=a.c/b.offsetHeight;d="scale("+a.width/a.c+") translateX(0.625rem)";b=b.style;b.f=d;b.g=d;b.MozTransform=d;b.webkitTransform=
d;b.transform=d;a.a.style.height=a.width/g+"px";for(var h=a.a.getElementsByClassName("emotion--banner"),c=h.length,k=a.a.getElementsByClassName("image-slider"),l=k.length,b=a.a.getElementsByClassName("product-slider"),g=b.length,d=0;d<c;d++)q(h[d]);for(d=0;d<l;d++)for(var h=k[0],c=h.getElementsByClassName("image-slider--item"),r=c.length,p=0;p<r;p++)q(h,c[p],"banner-slider--banner");for(d=0;d<g;d++)if(c=b[d],c.dataset.mode&&"ajax"==c.dataset.mode)c.getElementsByClassName("product-slider--container")[0].innerHTML+=
"<div class='js--loading-indicator indicator--absolute'><i class='icon--default' ></i></div>";else if(c.dataset.itemminwidth)for(k=100/Math.floor(c.clientWidth/c.dataset.itemminwidth),l=c.getElementsByClassName("product-slider--item"),h=l.length,c.getElementsByClassName("product-slider--container")[0].className+=" is--horizontal",c=0;c<h;c++)l[c].style.width=k+"%"}}}var a=m=void 0;try{n()}catch(e){}})();

{/literal}

{literal}
(function(){
    
    var opts = {};
    
    function getState()
    {
        var width = window.innerWidth;
        
        if (typeof width != 'number')
        {
            return -1;
        }
        
        if (width < 480)
        {
            return {name: 'xs', number: 4};
        }
        
        if (width < 768)
        {
            return {name: 's', number: 3};
        }
        
        if (width < 1024)
        {
            return {name: 'm', number: 2};
        }
        
        if (width < 1260)
        {
            return {name: 'l', number: 1};
        }
        
        return {name: 'xl', number: 0};
    }
    
    function getWidth()
    {
        
        return opts.current.wrapper.offsetWidth;
    }
    
    function prepareEmotion()
    {
        var rows,
            mode,
            container = opts.current.wrapper.getElementsByClassName('emotion--container')[0];
        
        opts.current.width = getWidth();
        
        mode = container.dataset['gridmode'];
        
        if (mode == 'resize')
        {
            initResizeMode(container);
        }
        else
        {
            return;
        }

        initElements();
    }
    
    function initResizeMode(container)
    {
        var baseWidth = ~~container.dataset['basewidth'],
            cellSpacing = ~~container.dataset['cellspacing'];
    
        opts.current.baseWidth = baseWidth;
    
        container.style.width = baseWidth + cellSpacing + 'px';
        
        scale(container);
    }
    
    function setHeights(container)
    {   
        for (var i = 1; i <= rows; i++)
        {
            var elements = container.querySelectorAll('.emotion--element.row-' + opts.state.name + '-' + i),
                elementsLength = elements.length,
                height = 100 / rows * i;
        
            for (var z = 0; z < elementsLength; z++)
            {
                elements[z].style.height = height + '%';
            }
        }
    }
    
    function setPositions(container)
    {
        for (var i = 1; i < rows; i++)
        {
            var elements = container.querySelectorAll('.emotion--element.start-row-' +opts.state.name + '-' + i ),
                elementsLength = elements.length,
                start = 100 / rows * ( i - 1 );
        
            for (var z = 0; z < elementsLength; z++)
            {
                elements[z].style.top = start + '%';
            }
        }
    }
    
    function scale(container)
    {
        var ratio = opts.current.baseWidth / container.offsetHeight,
            factor = opts.current.width / opts.current.baseWidth,
            transform = 'scale(' + factor + ') translateX(0.625rem)',
            style = container.style;
        
        style.MSTransform = transform;
        style.OTransform = transform;
        style.MozTransform = transform;
        style.webkitTransform = transform;
        style.transform = transform;
        
        opts.current.wrapper.style.height = opts.current.width / ratio + 'px';
    }
    
    function initElements()
    {
        var banner = opts.current.wrapper.getElementsByClassName('emotion--banner'),
            bannerLength = banner.length,
            imageSlider = opts.current.wrapper.getElementsByClassName('image-slider'),
            imageSliderLength = imageSlider.length,
            productSlider = opts.current.wrapper.getElementsByClassName('product-slider'),
            productSliderLength = productSlider.length;
        
        for (var i = 0; i < bannerLength; i++)
        {
            prepareBanner(banner[i]);
        }
        
        for (var i = 0; i < imageSliderLength; i++)
        {
            prepareImageSlider(imageSlider[0]);
        }
        
        for (var i = 0; i < productSliderLength; i++)
        {
            prepareProductSlider(productSlider[i]);
        }
    }
    
    function prepareBanner(bannerWrapper, bannerItem, selector)
    {
        var banner = (bannerItem) ? bannerItem : bannerWrapper,
            contentSelector = (selector) ? selector : 'banner--content',
            content = banner.getElementsByClassName(contentSelector)[0],
            size = window.getComputedStyle(bannerWrapper),
            width = size.width,
            height = size.height,
            bannerRatio = width / height,
            imgWidth = banner.dataset['width'],
            imgHeight = banner.dataset['height'],
            imgRatio = imgWidth / imgHeight,
            orientation = imgRatio > bannerRatio,
            contentWidth = orientation ? height * imgRatio + 'px' : '100%',
            contentHeight = orientation ? '100%': width / imgRatio + 'px' ;
    
        content.style.width = contentWidth;
        content.style.height = contentHeight;
    }
    
    function prepareImageSlider(slider)
    {
        var items = slider.getElementsByClassName('image-slider--item'),
            itemsLength = items.length;
        
        for (var i = 0; i < itemsLength; i++)
        {
            prepareBanner(slider, items[i], 'banner-slider--banner');
        }
    }
    
    function prepareProductSlider(slider)
    {
        if (slider.dataset['mode'] && slider.dataset['mode'] == 'ajax')
        {
            var indicator = "<div class='js--loading-indicator indicator--absolute'><i class='icon--default' ></i></div>";
            
            slider.getElementsByClassName('product-slider--container')[0].innerHTML += indicator;
            // show Loading Indicator
            return;
        }
        
        if (!slider.dataset['itemminwidth'])
        {
            // vertical slider are not supported
            return;
        }
        
        var containerWidth = slider.clientWidth,
            itemWidth = slider.dataset['itemminwidth'],
            itemsPerPage = Math.floor(containerWidth / itemWidth),
            itemPercentage = 100 / itemsPerPage,
            items = slider.getElementsByClassName('product-slider--item'),
            itemsLength = items.length;
    
        slider.getElementsByClassName('product-slider--container')[0].className += ' is--horizontal';
    
        for (var i = 0; i < itemsLength; i++)
        {
            items[i].style.width = itemPercentage + '%';
        }
    }
    
    function domReady()
    {
        opts.state   = getState();
        
        var wrappers = document.getElementsByClassName('emotion--wrapper'),
            wrappersLength = wrappers.length,
            overlay = document.getElementsByClassName('emotion--overlay');
    
        if (overlay.length > 0)
        {
            document.getElementsByClassName('emotion--overlay')[0].outerHTML = '';
        }
    
        for ( var i = 0; i < wrappersLength; i++ )
        {
            if (wrappers[i].dataset['availabledevices'].indexOf(opts.state.number) == -1)
            {
                wrappers[i].outerHTML = '';
            }
            else
            {
                opts.current = {};
                opts.current.wrapper = wrappers[i];
                
                prepareEmotion();
            }
        }
    }
    
    
    try
    {
        //console.log(Date.now());
        domReady();
        //console.log(Date.now());
    }
    catch(e)
    {
        //console.log('HTML not loaded');
        //console.log(e);
    }
    
    //document.addEventListener('DOMContentLoaded', function(){console.log(Date.now());});
})();
{/literal}*}
</script>
{/block}