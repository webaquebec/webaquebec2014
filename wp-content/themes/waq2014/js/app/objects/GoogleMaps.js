(function(){window.CustomGmap=(function(){CustomGmap.prototype.settings={markers:[{coord:[46.817682,-71.2065922],isWaq:true,content:'<a href="https://maps.google.ca/maps?q=ESPACE+400E+BELL+100,+QUAI+SAINT-ANDR%C3%89+QU%C3%89BEC,+QC&hl=fr&ie=UTF8&hq=ESPACE+400E+BELL+100,+QUAI+SAINT-ANDR%C3%89+QU%C3%89BEC,+QC&t=m&z=16&iwloc=A" target="_blank"><span class="name">Espace 400e Bell</span><span class="road">100, Quai Saint-André</span><span class="city">Québec, QC</span></a>',image:template_url+"/img/logo-waq-gmap.png"}]};function CustomGmap(elementId){if($(elementId).length>0){var coord,gMapOptions,isMobile,mapStyle,styledMap,zoomControlChoice;if($(window).width()>1166){coord=new google.maps.LatLng(46.818814,-71.201094);zoomControlChoice=true;}
else if($(window).width()>640){coord=new google.maps.LatLng(46.818682,-71.208175);zoomControlChoice=false;}
else{coord=new google.maps.LatLng(46.819129,-71.2065922);zoomControlChoice=false;}
isMobile=$('body').hasClass('mobile')?true:false;gMapOptions={zoom:16,center:coord,mapTypeControl:false,streetViewControl:false,panControl:false,scrollwheel:false,zoomControl:zoomControlChoice,mapTypeId:google.maps.MapTypeId.ROADMAP};if(isMobile){gMapOptions.draggable=false;}
mapStyle=[{"featureType":"poi","stylers":[{"hue":"#005eff"},{"lightness":-6},{"saturation":-100}]},{"featureType":"water","stylers":[{"invert_lightness":true},{"visibility":"on"},{"color":"#0086b8"}]},{"featureType":"road","stylers":[{"visibility":"on"},{"hue":"#0099ff"},{"gamma":1.13}]},{"featureType":"landscape","stylers":[{"saturation":-100}]}];this.map=new google.maps.Map($(elementId)[0],gMapOptions);styledMap=new google.maps.StyledMapType(mapStyle,{name:"Styled Map"});this.map.mapTypes.set('map_style',styledMap);this.map.setMapTypeId('map_style');this.addMarker();}}
CustomGmap.prototype.addMarker=function(){var iconHeight,iconWidth,iconmid,key,marker,markerCoord,opts,shadowHeight,shadowWidth,shadowmid,_ref,_results,_this=this;this.marker=[];_ref=this.settings.markers;_results=[];var length=_ref.length,marker=null;for(var i=0;i<length;i++){marker=_ref[i];key=i;markerCoord=new google.maps.LatLng(marker["coord"][0],marker["coord"][1]);this.marker[key]={};opts={coord:markerCoord,content:marker.content,image:marker.image,alwaysOpen:marker.isWaq!=null};this.marker[key]["infoWindow"]=new CustomInfoWindow(this.map,opts);if(marker.icon!=null){iconWidth=marker.icon.width;iconHeight=marker.icon.height;iconmid=[iconWidth/2,iconHeight/2];this.marker[key]["icon"]=new google.maps.MarkerImage(marker.icon.src,null,null,new google.maps.Point(iconmid[0],iconmid[1]),new google.maps.Size(iconWidth,iconHeight));if(marker.shadow!=null){shadowWidth=marker.shadow.width;shadowHeight=marker.shadow.height;shadowmid=[shadowWidth/2,shadowHeight/2];this.marker[key]["shadow"]=new google.maps.MarkerImage(marker.shadow.src,null,null,new google.maps.Point(shadowmid[0],shadowmid[1]),new google.maps.Size(shadowWidth,shadowHeight));}
this.marker[key]["marker"]=new google.maps.Marker({position:markerCoord,map:this.map,icon:this.marker[key]["icon"],shadow:this.marker[key]["shadow"]!=null?this.marker[key]["shadow"]:false,visible:true,draggable:false,cursor:"pointer"});_results.push(google.maps.event.addListener(this.marker[key]["marker"],'click',function(e){if(_this.currentOpenedInfoWindow!=null){_this.currentOpenedInfoWindow.close();}
_this.marker[key]["infoWindow"].open();return _this.currentOpenedInfoWindow=_this.marker[key]["infoWindow"];}));}else{_results.push(void 0);}}
return _results;};return CustomGmap;})();window.CustomInfoWindow=(function(){function CustomInfoWindow(map,opts){var closeBtn,isMobile,wrap,_this=this;this.position=opts.coord;this.alwaysOpen=opts.alwaysOpen;this.map=map;closeBtn=!this.alwaysOpen?"<span class=\"closeBtn\">×</span>":"";isMobile=$('body').hasClass('mobile')?true:false;if(!isMobile){wrap="<div class=\"customInfoWindow\">";}else{wrap="<div class=\"customInfoWindow mobile\">";}
wrap+="      "+ closeBtn+"      <div class=\"padding\">        <span class=\"address\">          "+ opts.content+"        </span>";if(!isMobile){wrap+="<img src=\""+ opts.image+"\" />";}
wrap+="</div>";if(!isMobile){wrap+="<span class=\"shadow\"></span>";}
wrap+="</div>";this.wrap=$(wrap);this.setMap(this.map);this.isVisible=true;this.wrap.find('.closeBtn').on('click',function(){if(typeof console!=="undefined"&&console!==null){console.log("click to close");}
return _this.close();});}
CustomInfoWindow.prototype=new google.maps.OverlayView();CustomInfoWindow.prototype.onAdd=function(){var cancelHandler,event,events,panes,_i,_len,_results;this.wrap.css({opacity:this.alwaysOpen?1:0,position:"absolute"});panes=this.getPanes();panes.overlayMouseTarget.appendChild(this.wrap[0]);this.iWidth=this.wrap.outerWidth();this.iHeight=this.wrap.outerHeight();cancelHandler=function(e){e.cancelBubble=true;if(e.stopPropagation){return e.stopPropagation();}};events=['mousedown','touchstart','touchend','touchmove','contextmenu','click','dblclick','mousewheel','DOMMouseScroll'];this.listeners=[];_results=[];for(_i=0,_len=events.length;_i<_len;_i++){event=events[_i];_results.push(this.listeners.push(google.maps.event.addDomListener(this.wrap[0],event,cancelHandler)));}
return _results;};CustomInfoWindow.prototype.open=function(){return this.wrap.show();};CustomInfoWindow.prototype.close=function(){return this.wrap.hide();};CustomInfoWindow.prototype.draw=function(){var overlayProjection,pos,wrapImg,wrapImgHeight;overlayProjection=this.getProjection();pos=overlayProjection.fromLatLngToDivPixel(this.position);this.oX=pos.x- this.wrap.outerWidth()/2;this.oY=pos.y- this.wrap.outerHeight()- 30;wrapImg=this.wrap.find('img');wrapImgHeight=wrapImg.height();this.wrap.find('img').css({'top':'50%','margin-top':-(wrapImgHeight/2)});return this.wrap.css({left:this.oX,top:this.oY,opacity:1,display:this.alwaysOpen?'block':'none'});};return CustomInfoWindow;})();}).call(this);function GoogleMapResize(){google.maps.event.trigger(WAQ.GoogleMap.map,'resize');if($(window).width()>1166){WAQ.GoogleMap.map.setCenter(new google.maps.LatLng(46.818814,-71.201094));WAQ.GoogleMap.map.set('zoomControl',true);}
else if($(window).width()>640){WAQ.GoogleMap.map.setCenter(new google.maps.LatLng(46.818682,-71.208175));WAQ.GoogleMap.map.set('zoomControl',false);}
else{WAQ.GoogleMap.map.setCenter(new google.maps.LatLng(46.819129,-71.2065922));WAQ.GoogleMap.map.set('zoomControl',false);}
var length=WAQ.GoogleMap.marker.length,marker=null;for(var i=0;i<length;i++){marker=WAQ.GoogleMap.marker[i];marker.infoWindow.draw();}}
$(window).resize(function(){GoogleMapResize();});