/*!
 * Plugin name : buttonize
 * Plugin description : Transform any html tag in <button>
 * Author: based on anyTagIntoBtn by Hugo Soucy <hugo@soucy.cc>
 * @usage : $('a').buttonize()
 * @return : $(button) collection
 */
;(function($, window, document, undefined) {

    var defaultConfig = {
        a11y        : false,
        a11yText    : 'Cliquer pour ouvrir'
    };
    
    var Buttonize = function(tag, config) {
        this.config = $.extend({}, defaultConfig, config);
        this.tag    = $(tag);
    };

    Buttonize.prototype.init = function() {
        var tagHtml         = this.tag.html(),
            tagAttr         = this.getAttributes(this.tag[ 0 ]),
            tagAria         = this.config.a11y ? ' aria-live="polite"': '',
            tagA11yText     = this.config.a11y ? '<span class="visuallyhidden">' + this.config.a11yText + '</span>' : '',
            button          = '<button ' + tagAttr.join(' ') + tagAria + '>' + tagHtml + tagA11yText + '</button>';

        this.tag.replaceWith(button);
        return $(button);
    };

    Buttonize.prototype.getAttributes = function(tag) {
        return $.map(tag.attributes, function(atrb) {
            var _atrb,
                name = atrb.name || atrb.nodeName,
                value = $(tag).attr(name),
                hregRegEx = /href/gi;

            if (value === undefined || value === false) return;

            _atrb = name + '="' + value + '"';
            return _atrb.match(hregRegEx) ? _atrb = _atrb.replace(hregRegEx, 'data-href') : _atrb;
        });
    };

    $.fn.buttonize = function(config) {
        return this.map(function() {
            var button = new Buttonize(this, config);
            return button.init().toArray();
        });
    };

})(jQuery, window, document);