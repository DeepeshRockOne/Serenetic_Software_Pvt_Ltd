/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function(config) {
  // The toolbar groups arrangement, optimized for two toolbar rows.
  config.filebrowserUploadUrl = '../thirdparty/ckeditor/upload.php';
  config.filebrowserImageBrowseLinkUrl = '../thirdparty/ckeditor/browser.php?type=Images&dir=..%2F..%2Fuploads%2Fckeditor&CKEditor=content&CKEditorFuncNum=1&langCode=en';
//	config.filebrowserImageBrowseUrl = '../thirdparty/ckeditor/browser.php';
  config.filebrowserImageBrowseUrl = '../thirdparty/ckeditor/browser.php?type=Images&dir=' + encodeURIComponent('../../uploads/ckeditor');

  config.toolbar = [
   /* {name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo']},
    {name: 'spellchecker', items: ['Scayt']},
    {name: 'links', items: ['Link', 'Unlink', 'Anchor']},
    {name: 'insert', items: ['Image', 'MyImage', 'Flash', 'Table', 'HorizontalRule', 'SpecialChar']},*/
    
    
    {name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike']},
    
    {name: 'paragraph', items: [ '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']},
    {name: 'styles', items: ['Format', 'Font', 'FontSize']},
    {name: 'colors', items: ['TextColor', 'BGColor']}
  ];

  config.toolbarGroups = [
    
    {name: 'basicstyles'},
    {name: 'paragraph'},
    {name: 'styles'}
  ];
  config.extraPlugins = 'filebrowser,evpvideo,myimage';
  config.title = false;
  //config.fillEmptyBlocks = false;
//  config.extraPlugins = 'imagebrowser';	
  config.enterMode = CKEDITOR.ENTER_BR;
  config.shiftEnterMode = CKEDITOR.ENTER_BR;
};
