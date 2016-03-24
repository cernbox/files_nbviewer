function closePublicFileCallback()
{
	$('#imgframe').empty();
	$('#imgframe').text('Reloading...');
	window.location.reload();
}

function loadFile(file, dir)
{
	var url = OC.filePath('files_nbviewer', 'ajax', 'loadfile.php') + "?file=" + encodeURIComponent(file) 
		+ "&dir=" + encodeURIComponent(dir) + "&requesttoken=" + encodeURIComponent(oc_requesttoken);
	
	if($('#isPublic').attr('value') == '1')
	{
		url = url + "&token=" + encodeURIComponent($('#sharingToken').attr('value'));
	}
	
	$.get(url).done(function(result)
	{
		if(result.status === 'success')
		{
			$('#nbviewer-loader').remove();
			var iFrame = $('#nbviewer-frame');
            var doc = iFrame[0].contentDocument || iFrame[0].contentWindow.document;
            doc.write(result.data.content);
            doc.close();
		}
		else
		{
			window.alert('There was a problem when loading your notebook: ' + result.data.message);
			closeFile();
		}
	});
}

function loadPublicFile(token)
{
	var url = OC.filePath('files_nbviewer', 'ajax', 'loadpublicfile.php') + "?token=" + encodeURIComponent(token) 
	+ "&requesttoken=" + encodeURIComponent(oc_requesttoken);

	$.get(url).done(function(result)
	{
		if(result.status === 'success')
		{
			$('#nbviewer-loader').remove();
			var iFrame = $('#nbviewer-frame');
	        var doc = iFrame[0].contentDocument || iFrame[0].contentWindow.document;
	        doc.write(result.data.content);
	        doc.close();
		}
		else
		{
			window.alert('There was a problem when loading your notebook: ' + result.data.message);
			closeFile(closePublicFileCallback);
		}
	});
}

function checkFile(directory, file)
{
	var sizeURL = OC.filePath('files_nbviewer', 'ajax', 'canbeopen.php') + "?file=" + encodeURIComponent(file) + "&dir=" + encodeURIComponent(directory);
	if($('#isPublic').attr('value') == '1')
	{
		sizeURL = sizeURL + "&token=" + encodeURIComponent($('#sharingToken').attr('value'));
	}
	
	$.get(sizeURL)
	.done(function(result){
		if(result.status === 'success')
		{
			loadFile(file, directory);
		}
		else
		{
			window.alert('The file you tried to open does not meet the requirements to be loaded: ' + result.data.message);
			closeFile();
		}
	})
}

function checkPublicFile(token)
{
	var sizeURL = OC.filePath('files_nbviewer', 'ajax', 'canbeopenpublic.php') + "?token=" + encodeURIComponent(token);
	$.get(sizeURL)
	.done(function(result){
		if(result.status === 'success')
		{
			loadPublicFile(token);
		}
		else
		{
			window.alert('The file you tried to open does not meet the requirements to be loaded: ' + result.data.message);
			closeFile(closePublicFileCallback());
		}
	})
}

/**
 * Set up all html elements needed to display the notebook
 */
function setUpEditor(closeCallBack)
{
	isNotebookOpen =  true;
	var mainDiv = $('#nbviewer');
	if(mainDiv.length < 1)
	{
		mainDiv = $('<div id="nbviewer"></div>');
		mainDiv.css('position', 'absolute');
		mainDiv.css('top', '0');
		mainDiv.css('left', '0');
		mainDiv.css('width', '100%');
		mainDiv.css('height', '100%');
		mainDiv.css('z-index', '200');
		mainDiv.css('background-color', '#fff');
		
		var frame = $('<iframe id="nbviewer-frame"></iframe>');
		frame.css('position', 'absolute');
		frame.css('top', '0');
		frame.css('left', '0');
		frame.css('width', '100%');
		frame.css('height', '100%');
		
		mainDiv.append(frame);
		$('#content').append(mainDiv);
		//$(document.body).append(mainDiv);
	}
	
	var loadingImg = $('<div id="nbviewer-loader"></div>');
	loadingImg.css('position', 'absolute');
	loadingImg.css('top', '50%');
	loadingImg.css('left', '50%');
	loadingImg.css('width', 'auto');
	loadingImg.css('height', 'auto');
	var img = OC.imagePath('core', 'loading-dark.gif');
	var imgContent = $('<img></img>');
	imgContent.attr('src',img);
	loadingImg.append(imgContent);
	
	var closeButton = $('<div></div>');
	closeButton.css('position', 'absolute');
	closeButton.css('top', '0');
	closeButton.css('left', '95%');
	closeButton.css('width', 'auto');
	closeButton.css('height', 'auto');
	closeButton.css('z-index', '200');
	closeButton.css('background-color', '#f00');
	var closeImg = OC.imagePath('core', 'actions/close.svg');
	var closeImgContent = $('<img></img>');
	closeImgContent.attr('src', closeImg);
	closeButton.append(closeImgContent);
	
	closeButton.click(function() { closeFile(closeCallBack); });
	
	$('#app-navigation').hide();
	$('#app-content').hide();
	
	mainDiv.append(loadingImg);
	mainDiv.append(closeButton);
}

/**
 * Open the notebook viewer and displays the notebook given the directory where it belongs and it's name
 * @param directory
 * @param file
 */
function openFile(directory, file)
{
	setUpEditor();
	checkFile(directory, file);
}

/**
 * Open the notebook viewer and displays the notebook given by a publically shared by link
 * @param token
 */
function openPublicFile(token)
{
	setUpEditor(closePublicFileCallback);
	checkPublicFile(token);
}


function closeFile(callback)
{
	if(isNotebookOpen)
	{
		$('#nbviewer').remove();
		$('#app-navigation').show();
		$('#app-content').show();
		isNotebookOpen = false;
		
		if(callback)
		{
			callback();
		}
	}
}

var isNotebookOpen = false;
$(document).ready(function () {
	if (typeof FileActions !== 'undefined') {
		FileActions.setDefault('application/pynb', 'Edit');
        OCA.Files.fileActions.register('application/pynb', 'Edit', OC.PERMISSION_READ, '', function (filename) {
        	openFile(FileList.getCurrentDirectory(), filename);
        });
	}
});