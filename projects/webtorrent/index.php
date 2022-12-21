<!DOCTYPE html>
<html>
<link rel="stylesheet" href="/styles.css">
  <body>
    <a href="/">Home</a>
    <h1>Download files using the WebTorrent protocol (BitTorrent over WebRTC).</h1>
    <h3>You may need to scroll down to see the video.</h3>

    <form>
      <label for="torrentId">Download from a magnet link: </label>
      <input name="torrentId", placeholder="magnet:" value="magnet:?xt=urn:btih:08ada5a7a6183aae1e09d831df6748d566095a10&dn=Sintel&tr=udp%3A%2F%2Fexplodie.org%3A6969&tr=udp%3A%2F%2Ftracker.coppersurfer.tk%3A6969&tr=udp%3A%2F%2Ftracker.empire-js.us%3A1337&tr=udp%3A%2F%2Ftracker.leechers-paradise.org%3A6969&tr=udp%3A%2F%2Ftracker.opentrackr.org%3A1337&tr=wss%3A%2F%2Ftracker.btorrent.xyz&tr=wss%3A%2F%2Ftracker.fastcast.nz&tr=wss%3A%2F%2Ftracker.openwebtorrent.com&ws=https%3A%2F%2Fwebtorrent.io%2Ftorrents%2F&xs=https%3A%2F%2Fwebtorrent.io%2Ftorrents%2Fsintel.torrent" style="width: 80%;">
      <button type="submit">Download</button>
    </form>

    <h3>Log</h3>
    <div class="log"></div>

    <!-- Include the latest version of WebTorrent -->
    <script src="https://cdn.jsdelivr.net/npm/webtorrent@latest/webtorrent.min.js"></script>

    <script>
      const client = new WebTorrent()

      client.on('error', function (err) {
        console.error('ERROR: ' + err.message)
      })

      document.querySelector('form').addEventListener('submit', function (e) {
        e.preventDefault() // Prevent page refresh

        const torrentId = document.querySelector('form input[name=torrentId]').value
        log('Adding ' + torrentId)
        client.add(torrentId, onTorrent)
      })

      function onTorrent (torrent) {
        log('Got torrent metadata!')
        log(
          'Torrent info hash: ' + torrent.infoHash + ' ' +
          '<a href="' + torrent.magnetURI + '" target="_blank">[Magnet URI]</a> ' +
          '<a href="' + torrent.torrentFileBlobURL + '" target="_blank" download="' + torrent.name + '.torrent">[Download .torrent]</a>'
        )

        // Print out progress every 5 seconds
        const interval = setInterval(function () {
          log('Progress: ' + (torrent.progress * 100).toFixed(1) + '%')
        }, 5000)

        torrent.on('done', function () {
          log('Progress: 100%')
          clearInterval(interval)
        })

        // Render all files into to the page
        torrent.files.forEach(function (file) {
          file.appendTo('.log')
          log('(Blob URLs only work if the file is loaded from a server. "http//localhost" works. "file://" does not.)')
          file.getBlobURL(function (err, url) {
            if (err) return log(err.message)
            log('File done.')
            log('<a href="' + url + '">Download full file: ' + file.name + '</a>')
          })
        })
      }

      function log (str) {
        const p = document.createElement('p')
        p.innerHTML = str
        document.querySelector('.log').appendChild(p)
      }
    </script>
  </body>
</html>
