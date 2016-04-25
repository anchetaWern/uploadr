
Dropzone.autoDiscover = false;

var uploadr = new Dropzone(document.getElementById('drop'), {
	url: "/upload",                        
	autoProcessQueue: false,
	acceptedFiles: 'image/png,image/gif,image/jpeg,image/svg+xml',
	uploadMultiple: true,
	maxFilesize: 5,
	maxFiles: 2,
	success: function(file, response){
		
		//window.location.href = response.share_url;
	}
});

$('#upload').click(function(){           
    uploadr.processQueue();
});