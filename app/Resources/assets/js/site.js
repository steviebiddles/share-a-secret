(function () {
    var clipboard = new Clipboard('.copy-trigger');

    clipboard.on('success', function(e) {
        //console.info('Text:', e.text);
        $('.copy-trigger').text('Copied!');
        e.clearSelection();
    });
})();