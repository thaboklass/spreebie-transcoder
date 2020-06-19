=== Spreebie Transcoder – Resize, Compress and Store Video ===
Contributors: Thabo David Klass
Donate link: http://openbeacon.biz/spreebie-transcoder-video-transcoding-for-wordpress-with-ffmpeg/
Tags: video, ffmpeg, resizing, compression, google cloud storage
Requires at least: 4.1
Tested up to: 5.1
Requires PHP: 5.5
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

SPREEBIE TRANSCODER is a WordPress plugin that resizes, compresses and stores MP4 video via FFmpeg and Google Cloud Storage.

== Description ==

The  role of video as the premier mode of communication and information transmission in the year 2019 has now become uncontested. Video informs all aspects of our lives, from how we communicate with our loved ones to how radio stations stream content to their ‘listeners’. The monumental task for content providers today is to provide video in a manner in which it is accessible to everyone - not providing video is totally out of the question.

[youtube https://www.youtube.com/watch?v=Gek1h29pyNU]

A large part of this is of course driven by the all internet-based media distribution platforms that have sprouted up since 2005. Video has become so instrumental and so effective that it has even triggered the biggest learning cycle in human history. So, the important test for content providers is fundamentally related to access - global access. Pertaining to internet infrastructure, different places in the world are at different stages of development - all those people have to have access to the same content. Providing video at resolutions that deal with these scenarios can be what separates a content providers from their competition.

The MP4 video format and FFmpeg have matured tremendously over the past 15 years. MP4 has become the de facto video format on the web and FFmpeg is the goto transcoding tool for some of the biggest video distribution platforms in the world. Just because of the lay of the land in 2019, it is important to use video on your site to interact with your visitors in ways that they can relate to and understand. Communicating in a standard way using the MP4 video format and making sure those videos can be provided in sizes and rates that respond to individual users’ needs and circumstances is imperative.

WordPress is now running on 30% of all websites – in a way, it has become a kind of web operating system. This means that it has become unavoidable and thinking about creating new products and dedicated implementations of existing products for WordPress is imperative.

The combination of better video transcoding technology, standard (popular) video containers, cloud storage services and a web dominated by a very powerful WordPress was fertile soil to create Spreebie Transcoder.

= Features =
1. **Resizing** - Spreebie Transcoder resizes video to lesser resolutions. For example, a 360p video will get 240p and 144p copies.
2. **Compression** - Spreebie Transcoder has adjustable settings to determine the quality of video and the speed at which transcoding happens. This results in compression to varying degrees depending on what settings have been chosen.
3. **Google Cloud Storage** - If you choose to, transcoded video can be stored on Google Cloud Storage as a backup.
4. **Folders** - Spreebie Transcoder supports WP Real Media Library that brings folder functionality to WordPress media.
5. **Support and Manual** - Spreebie Transcoder provides a support tab through which users can request support from Spreebie representatives. A comprehensive manual is also provided to help users with most of the questions they may have.

This plugin requires FFmpeg to work.

THIRD PARTY PRODUCT AND SERVICE NOTE: When storing already transcoded video on the cloud with this plugin, a third party service called Google Cloud Storage (https://cloud.google.com/storage/) is used for storage. A number of open-source PHP libraries from various sources are packaged within Spreebie Transcoder in the folder "vendor" in order to facilitate Google Cloud Storage functionality. Google Cloud Storage in this plugin ONLY works for cloud storage – the rest of the plugin can function without it.

Another third party product which is not included with this plugin but is its centre is FFmpeg is (https://www.ffmpeg.org/) – this is used to perform all the video analysis and transcoding. Without FFmpeg, this plugin cannot function.

The last third party product that can be used in tandem with this plugin but is not included with it is WP Real Media Library (https://matthias-web.com/wordpress/real-media-library/). This can be used to organise your transcoded media into folders. Spreebie Transcoder does not need WP Real Media Library to function.

== Installation ==

= Minimum Requirements =

* PHP version 5.5 or greater (PHP 5.6 or greater is recommended)
* MySQL version 5.0 or greater (MySQL 5.6 or greater is recommended)
* Spreebie Transcoder 1.0.1 requires WordPress 4.1+

1. Install the plugin like it is commonly done, either by uploading it via FTP or by using the
"Add Plugin" function of WordPress.

2. Activate the plugin on the plugin administration page.

3. Go to the “Spreebie Transcoder” and click the “Settings” tab.

4. Enter the FFmpeg and FFprobe paths.

5. You can then go to the “Transcode” tab to start transcoding.

= Usage =

1. Go to the “Spreebie Transcoder” menu and click it. Click on the “Settings” tab. Enter the paths to you FFmpeg and FFprobe installations. If you would like to use Google Cloud Storage as secondary storage, enter all the Google Cloud Storage details.

2. A comprehensive manual has also been provided to guide you through this process.  Click the “Support” tab and a link to the manual will be visible. You can also reach us by sending out a support request if you are still having trouble. 

3. To transcode a MP4 file, go to the “Transcode" tab. Choose the file, pick its category and write a short caption that describes the video. Click the “Transcode Video” button. This plugin works best for videos with 720p resolution and less.

4. After the video has been transcoded, go to “Spreebie Transcoded Media -> View Spreebie Transcoded Media”. Click on the video you’ve just transcoded. You can play the video to see if it plays to your liking. If you have enabled Google Cloud Storage, a “Store on GCS” button will be available - you can store your file on the Google Cloud by clicking it.

5. To view all the transcoded media and their screenshots, click the “Media” menu. All the transcoded videos will be available in there.


== Frequently Asked Questions ==

Q: What is FFmpeg and why is it required? A: "FFmpeg is a free software project consisting of a vast software suite of libraries and programs for handling video, audio, and other multimedia files and streams.” - Wikipedia
In the context of this plugin, it is a way to analyse a video’s properties, resize it and compress it.

Q: Where can I find FFmpeg? A: You can find FFmpeg at https://www.ffmpeg.org/.

Q: Does Spreebie Transcoder require Javascript to function? A: No.

Q: Why do I need both FFmpeg and FFprobe? A: FFprobe analyses your video’s properties before any resizing and trans-rating can be done by FFmpeg.

Q: Can this plugin function without FFmpeg? No.

Q: Can this plugin function without Google Cloud Storage enabled? Yes. In fact, the purpose of GCS in this plugin is to just to safely store your transcoded video as a backup.

Q: Is Spreebie Transcoder safe? A: As with all WordPress plugins, the security of your site is what keeps the site secure. The plugin uses ALL the standard security measures.

Q: Does the Google Cloud Storage third party service comply with the current EU data protection rules? A: Yes, it does.


== Screenshots ==

1. The transcoded Spreebie Transcoder Media video with a button to store it on Google Cloud Storage.

2. Transcoded videos and screenshots with resolution suffixed in their file names.

3. The Spreebie Transcoder backend with the "Transcoder" tab active.

4. The Spreebie Transcoder backend with the "Settings" tab active.

5. Clicking on one of the items on the "View Spreebie Transcoded Media" page will open one of the videos a user has transcoded.


== Changelog ==

= 1.0 =
Initial release

== Upgrade Notice ==

= 1.0 =
Initial release. No upgrade yet.
