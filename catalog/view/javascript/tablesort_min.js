!function(a){a.tablesort=function(b,c){var d=this;this.$table=b,this.settings=a.extend({},a.tablesort.defaults,c),this.$table.find("thead th").bind("click.tablesort",function(){a(this).hasClass("disabled")||d.sort(a(this))}),this.index=null,this.$th=null,this.direction=[]},a.tablesort.prototype={sort:function(b,c){var d=new Date,e=this,f=this.$table,g=f.find("tbody tr"),h=b.index(),i=[],j=a("<div/>"),k=function(a,b,c){var d;return a.data().sortBy?(d=a.data().sortBy,"function"==typeof d?d(a,b,c):d):b.data("sort")?b.data("sort"):b.text()},l=function(a,b){var q,r,s,c=/(^-?[0-9]+(\.?[0-9]*)[df]?e?[0-9]?$|^0x[0-9a-f]+$|[0-9]+)/gi,d=/(^[ ]*|[ ]*$)/g,e=/(^([\w ]+,?[\w ]+)?[\w ]+,?[\w ]+\d+:\d+(:\d+)?[\w ]?|^\d{1,4}[\/\-]\d{1,4}[\/\-]\d{1,4}|^\w+, \w+ \d+, \d{4})/,f=/^0x[0-9a-f]+$/i,g=/^0/,i=function(a){return(""+a).toLowerCase().replace(",","")},j=i(a).replace(d,"")||"",k=i(b).replace(d,"")||"",l=j.replace(c,"\0$1\0").replace(/\0$/,"").replace(/^\0/,"").split("\0"),m=k.replace(c,"\0$1\0").replace(/\0$/,"").replace(/^\0/,"").split("\0"),n=Math.max(l.length,m.length),o=parseInt(j.match(f),10)||1!=l.length&&j.match(e)&&Date.parse(j),p=parseInt(k.match(f),10)||o&&k.match(e)&&Date.parse(k)||null;if(p){if(p>o)return-1;if(o>p)return 1}for(s=0;n>s;s++){if(q=!(l[s]||"").match(g)&&parseFloat(l[s])||l[s]||0,r=!(m[s]||"").match(g)&&parseFloat(m[s])||m[s]||0,isNaN(q)!==isNaN(r))return isNaN(q)?1:-1;if(typeof q!=typeof r&&(q+="",r+=""),r>q)return-1;if(q>r)return 1}return 0};0!==g.length&&(e.$table.find("thead th").removeClass(e.settings.asc+" "+e.settings.desc),this.$th=b,this.direction[h]=this.index!=h?"desc":"asc"!==c&&"desc"!==c?"desc"===this.direction[h]?"asc":"desc":c,this.index=h,c="asc"==this.direction[h]?1:-1,e.$table.trigger("tablesort:start",[e]),e.log("Sorting by "+this.index+" "+this.direction[h]),g.sort(function(d,f){var g=a(d),h=a(f),j=g.index(),m=h.index();return i[j]?d=i[j]:(d=k(b,e.cellToSort(d),e),i[j]=d),i[m]?f=i[m]:(f=k(b,e.cellToSort(f),e),i[m]=f),l(d,f)*c}),g.each(function(a,b){j.append(b)}),f.append(j.html()),b.addClass(e.settings[e.direction[h]]),e.log("Sort finished in "+((new Date).getTime()-d.getTime())+"ms"),e.$table.trigger("tablesort:complete",[e]))},cellToSort:function(b){return a(a(b).find("td").get(this.index))},log:function(b){(a.tablesort.DEBUG||this.settings.debug)&&console&&console.log&&console.log("[tablesort] "+b)},destroy:function(){return this.$table.find("thead th").unbind("click.tablesort"),this.$table.data("tablesort",null),null}},a.tablesort.DEBUG=!1,a.tablesort.defaults={debug:a.tablesort.DEBUG,asc:"sorted ascending",desc:"sorted descending"},a.fn.tablesort=function(b){var c,e;return this.each(function(){c=a(this),e=c.data("tablesort"),e&&e.destroy(),c.data("tablesort",new a.tablesort(c,b))})}}(jQuery);

