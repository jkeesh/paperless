/*
 * This manages the themes. 
 *
 * @param   options, an object of parameters
 *
 * @author  Jeremy Keeshin December 24, 2011
 */
ThemeManager.themes = {
        'default':{
            'css': 'shCoreDefault.css',
            'bg': '#edeff4'
        },
        'ultra':{
            'css': 'shCoreMDUltra.css',
            'bg': '#428bdd'
        },
        'midnight':{
            'css': 'shCoreMidnight.css',
            'bg': '#428bdd'
        },
        'django':{
            'css': 'shCoreDjango.css',
            'bg': '#91bb9e'
        },
        'dark': {
            'css': 'shCoreRDark.css',
            'bg': '#878a85'
        },
        'eclipse': {
            'css': 'shCoreEclipse.css',
            'bg': '#3f5fbf'
        },
        'emacs': {
            'css': 'shCoreEmacs.css',
            'bg': '#ff7d27'
        },
        'gray': {
            'css': 'shCoreFadeToGrey.css',
            'bg': '#696854'
        }    
};

function ThemeManager(options){
    this.set_theme(options.theme);
    
    this.listen_to_changes();
}

ThemeManager.prototype.listen_to_changes = function(){
    var self = this;
    // Only if we are on the settings page.
    if($('#settings_theme_picker').length > 0){
        SyntaxHighlighter.all();
        var converter = new Showdown.converter();
        
        $('#settings_theme_picker').change(function(){
            self.set_theme($(this).val());
        });
    }
}

ThemeManager.prototype.set_theme = function(theme_name){
    this.name = theme_name;
    this.theme = ThemeManager.themes[this.name];
    var new_theme = root_url +'/static/js/syntaxhighlighter/styles/' + this.theme['css'];
    var new_color = this.theme['bg'];
    $('#syntaxStylesheet').attr('href', new_theme);
    $('body').css('background-color', new_color);
}