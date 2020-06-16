(function(){
if (typeof(igk) == "undefined")
	return;
igk.system.createNS("igk.core", {
'install':function(uri, t){
	var q = $igk(t).first();
	// console.debug("install start");
	return function(e){
		//console.debug("install start "+uri+ " base ::: complete"+e.readyState);
		
		if ((e.readyState != 4) || (e.status!=200))
			return;   
		if (window.EventSource){
			// console.debug("start event source "+uri);

			var source = new EventSource(uri);			
			source.addEventListener("message", function(e){
				// console.debug("message : "+e.data);
				q.setHtml(e.data);
			});
			
			source.addEventListener("error", function(e){
				if(e.readyState == EventSource.CLOSED){
					console.log("connection closed");
				}
			});
			
			source.addEventListener("finish", function(e){				
				source.close();
				q.setHtml('');
				if (e.data == 'ok'){
					document.location.reload(true);
				}
			});
		} else {			
			q.setHtml(igk.R.msg_installing);
			igk.ajx.post(uri, null, function(xhr){
				if (this.isReady()){ 
					q.setHtml('');
					document.location.reload(true);
				}
			});
		}
	};
},
'progress':function(t){
	var q = $igk(t).first();
	return function(e){
		// console.debug("start progress");
		q.setHtml("progress : "+( Math.round((e.loaded / e.total) * 100)) + "%");
	};
}}); 
})();