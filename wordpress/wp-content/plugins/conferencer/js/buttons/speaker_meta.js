conferencer.tinymce_button({
	slug: 'speaker_meta',
	title: "Speaker Meta",
	image: 'speaker_meta.png',
	onclick: function(editor) {
		editor.execCommand('mceInsertContent', 0, '[speaker_meta]');
	}
});