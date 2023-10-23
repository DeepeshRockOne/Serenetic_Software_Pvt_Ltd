/**
 * @license Copyright (c) 2003-2019, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For complete reference see:
	// https://ckeditor.com/docs/ckeditor4/latest/api/CKEDITOR_config.html
	//config.filebrowserUploadUrl = '../thirdparty/ckeditor_new/upload.php';
	//config.filebrowserImageBrowseLinkUrl = '../thirdparty/ckeditor_new/browser.php?type=Images&dir=..%2F..%2Fuploads%2Fckeditor&CKEditor=content&CKEditorFuncNum=1&langCode=en';
  //	config.filebrowserImageBrowseUrl = '../thirdparty/ckeditor/browser.php';
	//config.filebrowserImageBrowseUrl = '../thirdparty/ckeditor_new/browser.php?type=Images&dir=' + encodeURIComponent('../../uploads/ckeditor');
	config.filebrowserBrowseUrl='../thirdparty/ckeditor_new/browser.php?dir=' + encodeURIComponent('../../uploads/ckeditor');
  

	// The toolbar groups arrangement, optimized for two toolbar rows.
	/*config.toolbarGroups = [
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
		{ name: 'links' ,     groups: [ 'Link', 'Unlink', 'Anchor' ]},
		{ name: 'insert' },
		{ name: 'forms' },
		{ name: 'tools' },
		{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'others' },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'about' }
	];*/

	config.toolbar = [
		{name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', '-', 'Undo', 'Redo']},
		{name: 'links', items: ['Link', 'Unlink', 'Anchor']},
		{name: 'insert', items: ['Table']},
		{name: 'basicstyles', groups: ['basicstyles', 'cleanup'], items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat']},
		'/',
		{name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'], items: ['NumberedList', 'BulletedList', '-','CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language']},
		{name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize']},
		{name: 'colors', items: ['TextColor', 'BGColor']}
	  ];
	
	  config.toolbarGroups = [
		{name: 'paragraph'},
		{name: 'clipboard'},
		{name: 'links'},
		{name: 'insert'},
		{name: 'basicstyles'},
		{name: 'styles'},
		{name: 'colors'}
	  ];

	// Remove some buttons provided by the standard plugins, which are
	// not needed in the Standard(s) toolbar.
	//config.removeButtons = 'Underline,Subscript,Superscript';
	// Set the most common block elements.
	//config.format_tags = 'p;h1;h2;h3;pre';
	config.extraPlugins = 'filebrowser,myimage';
	// Simplify the dialog windows.
	//config.removeDialogTabs = 'image:advanced;link:advanced';
	config.enterMode = CKEDITOR.ENTER_BR;
  config.shiftEnterMode = CKEDITOR.ENTER_BR;
};
