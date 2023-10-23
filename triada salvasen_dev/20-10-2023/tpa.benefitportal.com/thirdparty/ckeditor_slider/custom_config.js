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
   
    {name: 'tools', items: ['Maximize']},
    {name: 'source', items: ['Source']},
    {name: 'basicstyles', groups: ['basicstyles', 'cleanup'], items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat']},
    '/',
    {name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'], items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language']},
    {name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize']},
    {name: 'colors', items: ['TextColor', 'BGColor']}
  ];

  config.toolbarGroups = [
    
    {name: 'tools'},
    {name: 'source'},
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
