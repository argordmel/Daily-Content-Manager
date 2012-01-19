function Menu(id) {    
    if (!document.getElementById || !document.getElementsByTagName)
        return false;
    this.menu = document.getElementById(id);
    this.submenus = this.menu.getElementsByTagName("div");
    this.remember = true;
    this.speed = 5;
    this.markCurrent = true;
    this.oneSmOnly = false;

    return true;
}
Menu.prototype.init = function() {
    var mainInstance = this;
    for (var i = 0; i < this.submenus.length; i++)
        this.submenus[i].getElementsByTagName("span")[0].onclick = function(){ mainInstance.toggleMenu(this.parentNode);};
    if (this.markCurrent) {
        var links = this.menu.getElementsByTagName("a");
        for (var j = 0; j < links.length; j++)
        if (links[j].href == document.location.href) {
            links[j].className = "current";
            break;
        }
    }
    if (this.remember) {
        var regex = new RegExp("principal_" + encodeURIComponent(this.menu.id) + "=([01]+)");
        var match = regex.exec(document.cookie);
        if (match) {
            var states = match[1].split("");
            for (var k = 0; k < states.length; k++)
                this.submenus[k].className = (states[k] == 0 ? "collapsed" : "");
        }
    }
};

Menu.prototype.toggleMenu = function(submenu) {
    if (submenu.className == "collapsed")
        this.expandMenu(submenu);
    else
        this.collapseMenu(submenu);
};

Menu.prototype.expandMenu = function(submenu) {
    var fullHeight = submenu.getElementsByTagName("span")[0].offsetHeight;
    var links = submenu.getElementsByTagName("a");
    for (var i = 0; i < links.length; i++)
        fullHeight += links[i].offsetHeight;
    var moveBy = Math.round(this.speed * links.length);
    var mainInstance = this;
    var intId = setInterval(function() {
        var curHeight = submenu.offsetHeight;
        var newHeight = curHeight + moveBy;
        if (newHeight < fullHeight)
            submenu.style.height = newHeight + "px";
        else {
            clearInterval(intId);
            submenu.style.height = "";
            submenu.className = "";
            mainInstance.memorize();
        }
    }, 30);
    this.collapseOthers(submenu);
};

Menu.prototype.collapseMenu = function(submenu) {
    var minHeight = submenu.getElementsByTagName("span")[0].offsetHeight;
    var moveBy = Math.round(this.speed * submenu.getElementsByTagName("a").length);
    var mainInstance = this;
    var intId = setInterval(function() {
        var curHeight = submenu.offsetHeight;
        var newHeight = curHeight - moveBy;
        if (newHeight > minHeight)
            submenu.style.height = newHeight + "px";
        else {
            clearInterval(intId);
            submenu.style.height = "";
            submenu.className = "collapsed";
            mainInstance.memorize();
        }
    }, 30);
};

Menu.prototype.collapseOthers = function(submenu) {
    if (this.oneSmOnly) {
        for (var i = 0; i < this.submenus.length; i++)
        if (this.submenus[i] != submenu && this.submenus[i].className != "collapsed")
        this.collapseMenu(this.submenus[i]);
    }
};

Menu.prototype.memorize = function() {
    if (this.remember) {
        var states = new Array();
        for (var i = 0; i < this.submenus.length; i++)
            states.push(this.submenus[i].className == "collapsed" ? 0 : 1);
        var d = new Date();
        d.setTime(d.getTime() + (30 * 24 * 60 * 60 * 1000));
        document.cookie = "principal_" + encodeURIComponent(this.menu.id) + "=" + states.join("") + "; expires=" + d.toGMTString() + "; path=/";
    }
};