<?php
/**
 * A media library image with srcset, size and lazy loading.
 * $media  media row (or null: prints nothing)
 * $sizes  sizes attribute, for example "120px" or "(max-width: 560px) 100vw, 400px"
 * $width  display width in pixels, for the width/height attributes
 * $alt    alternative text ('' for decorative images)
 * $class  optional class; $eager true for images in the first screen
 */
if (!$media) {
    return;
}
$isSvg = $media['ext'] === 'svg';
[$w, $h] = $isSvg ? [$width, $width] : ItTour\Media::size($media, $width);
?>
<img src="<?= e(media_url($media, $width * 2)) ?>"<?php if (!$isSvg): ?> srcset="<?= e(ItTour\Media::srcset($media)) ?>" sizes="<?= e($sizes) ?>"<?php endif ?> width="<?= e($w) ?>" height="<?= e($h) ?>" alt="<?= e($alt) ?>"<?php if (!empty($class)): ?> class="<?= e($class) ?>"<?php endif ?><?php if (empty($eager)): ?> loading="lazy"<?php endif ?> decoding="async">
