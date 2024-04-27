# Spreebie Transcoder - Resize, Compress and Store Video WordPress Plugin

## Description

Spreebie Transcoder is a WordPress plugin that resizes, compresses, and stores MP4 videos via FFmpeg and Google Cloud Storage. With the increasing role of video as the primary mode of communication, providing accessible video content has become essential for content providers. Spreebie Transcoder offers features to ensure videos are accessible to users with varying internet speeds and device capabilities.

[![Spreebie Transcoder](https://www.youtube.com/watch?v=Gek1h29pyNU)](https://www.youtube.com/watch?v=Gek1h29pyNU)

### Features

1. **Resizing**: Resize videos to lesser resolutions, catering to users with different internet speeds and device capabilities.
2. **Compression**: Adjustable settings for video quality and transcoding speed, resulting in compression to varying degrees.
3. **Google Cloud Storage**: Option to store transcoded videos on Google Cloud Storage for backup.
4. **Folders**: Supports WP Real Media Library for organizing transcoded media into folders.
5. **Support and Manual**: Provides a support tab for user inquiries and access to a comprehensive manual.

This plugin requires FFmpeg to work.

### Third Party Services

- **Google Cloud Storage**: Used for storing transcoded videos as a backup.
- **FFmpeg**: Used for video analysis and transcoding.
- **WP Real Media Library**: Optional for organizing transcoded media into folders.

## Installation

### Minimum Requirements:

- PHP version 5.5 or greater (PHP 5.6 or greater is recommended)
- MySQL version 5.0 or greater (MySQL 5.6 or greater is recommended)
- WordPress 4.1+

1. Install the plugin via FTP or through the WordPress "Add Plugin" function.
2. Activate the plugin in the WordPress plugin administration page.
3. Go to “Spreebie Transcoder” and click the “Settings” tab.
4. Enter the paths to FFmpeg and FFprobe installations.
5. Configure Google Cloud Storage details if needed.
6. Access the "Transcode" tab to start transcoding.

## Usage

1. Go to the “Spreebie Transcoder” menu and click the “Settings” tab. Enter paths to FFmpeg and FFprobe installations. Configure Google Cloud Storage details if necessary.
2. Access the manual via the "Support" tab for detailed instructions.
3. To transcode a MP4 file, go to the “Transcode" tab. Choose the file, select its category, and provide a short caption. Click “Transcode Video”.
4. After transcoding, go to “Spreebie Transcoded Media -> View Spreebie Transcoded Media”. Play the video to confirm quality. Optionally, store it on Google Cloud Storage.
5. All transcoded media can be accessed via the “Media” menu.

## Frequently Asked Questions

### What is FFmpeg and why is it required?

"FFmpeg is a free software project consisting of a vast software suite of libraries and programs for handling video, audio, and other multimedia files and streams.” - Wikipedia
It's used for video analysis, resizing, and compression.

### Where can I find FFmpeg?

You can find FFmpeg at [ffmpeg.org](https://www.ffmpeg.org/).

### Does Spreebie Transcoder require Javascript to function?

No, it doesn't.

### Why do I need both FFmpeg and FFprobe?

FFprobe analyzes video properties before resizing and transcoding by FFmpeg.

### Can this plugin function without FFmpeg?

No, FFmpeg is required for this plugin to function.

### Can this plugin function without Google Cloud Storage enabled?

Yes, it can. Google Cloud Storage is optional for storing transcoded videos as a backup.

### Is Spreebie Transcoder safe?

The plugin follows standard WordPress security measures.

### Does Google Cloud Storage comply with the current EU data protection rules?

Yes, it does.

## Screenshots

1. Transcoded Spreebie Transcoder Media video with a button to store it on Google Cloud Storage.
2. Transcoded videos and screenshots with resolution suffixed in their filenames.
3. Spreebie Transcoder backend with the "Transcoder" tab active.
4. Spreebie Transcoder backend with the "Settings" tab active.
5. Video details page with transcoded video.

## Changelog

### 1.0
Initial release

## License

- License: GPLv2 or later
- License URI: [http://www.gnu.org/licenses/gpl-2.0.html](http://www.gnu.org/licenses/gpl-2.0.html)

## Contributors

- Thabo David Klass
