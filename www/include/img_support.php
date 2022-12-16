<?php
# exif정보 출력
function ImgExif($Img) {
	if(!function_exists('exif_read_data')) return;
	if(!file_exists($Img)) return array('error'=>'파일없음');
	$exifData = @exif_read_data($Img);
	$ImgInfo = @exif_read_data($Img);
	if($exifData['Orientation'] == 6)  $degree = 90;
	else if($exifData['Orientation'] == 8) $degree = -90;
	else if($exifData['Orientation'] == 3) $degree = -180;
	return array('degree'=>$degree,'exif'=>$exifData, 'info'=>$ImgInfo);
}


/**
 * 회전값이 있는 사진을 보정한다.
 *
 * @param      <type>  $Img    이미지경로(DOCUMENT포함)
 */
function ImgRotate($Img) {

	if(!function_exists('exif_read_data')) return;
	if(!file_exists($Img)) return array('error'=>'파일없음');
	$exifData = @exif_read_data($Img);
	$ImgInfo = getimagesize($Img);
	if(isset($exifData['Orientation'])) {
		if($exifData['Orientation'] == 6)  $degree = 270;
		else if($exifData['Orientation'] == 8) $degree = 90;
		else if($exifData['Orientation'] == 3) $degree = 180;
		if($degree) {
			if($exifData['FileType'] == 1) {
				$source = imagecreatefromgif($Img);
				$source = imagerotate ($source , $degree, 0);
				imagegif($source, $Img);
			}
			else if($exifData['FileType'] == 2) {
				$source = imagecreatefromjpeg($Img);
				$source = imagerotate ($source , $degree, 0);
				imagejpeg($source, $Img);
			}
			else if($exifData['FileType'] == 3) {
				$source = imagecreatefrompng($Img);
				$source = imagerotate ($source , $degree, 0);
				imagepng($source, $Img);
			}

			imagedestroy($source);
		}
	}
}


// -------- 비율 썸네일 적용 (resize / crop) --------
//      $_thumb_dir - 디렉토리 -- "/" 로 끝나게 함.
//      $_thumb_img - 이미지
//      $_thumb_w - 목표넓이
//      $_thumb_h - 목표높이
function ImgThumb($_thumb_dir, $_thumb_img, $_thumb_w, $_thumb_h, $_mode='copy'){
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/wideimage/lib/WideImage.php');

	// 설정
	$config['source_image_old'] = $_thumb_dir.$_thumb_img;
	$config['source_image'] = $_thumb_dir.$_thumb_img;
	$config['source_path'] = $_thumb_dir;
	$config['source_name'] = $_thumb_img;
	$config['width'] = $_thumb_w;
	$config['height'] = $_thumb_h;
	$config['quality'] = 100;

	$target_width = $_thumb_w; // 목표 넓이
	$target_height= $_thumb_h; // 목표 높이
	$target_ratio = round($target_width / $target_height, 3);
	$src_size = getimagesize($config['source_image']);
	$src_width = $src_size[0]; // 원본 넒이
	$src_height = $src_size[1]; // 원본 높이
	$src_ratio = round($src_width / $src_height , 3);
	$image = WideImage::load($config['source_image']);

	// ----- 크기가 다를 경우 적용 -----
	if($target_width <> $src_width && $target_height <> $src_height) {

		// 비율에 따른 크기 설정
		$_trigger = "N";
		$config['width'] = $target_width;
		$config['height'] = $target_height;
		// 원본의 넓이 비율이 목표의 넓이 비율보다 작을 경우 - 목표의 넓이에 기준을 맞춤 - 그래야 자를 수 있는 여지가 생김
		if( $target_ratio > $src_ratio ) {
			$_trigger = 'H'; // 높이 조절함.
			$config['width'] = ceil($target_width);
			$config['height'] = ceil($src_height * $target_width / $src_width);
		}
		// 원본의 넓이 비율이 목표의 넓이 비율보다 클 경우 - 목표의 높이에 기준을 맞춤 - 그래야 자를 수 있는 여지가 생김
		else if( $target_ratio < $src_ratio ) {
			$_trigger = 'W'; // 넓이 조절함.
			$config['width'] = ceil($src_width * $target_height / $src_height);
			$config['height'] = ceil($target_height);
		}


		// ----- resize -----
		if($_mode == 'copy') { // 복사모드
			$config['source_name'] = $_thumb_w.'x'.$_thumb_h.'_'.$_thumb_img;
			$config['source_image'] = $_thumb_dir.$config['source_name'];
			@unlink($_thumb_dir.$config['source_image']); // 기존 이미지 삭제
		}
		$image->resize($config['width'], $config['height'])->saveToFile($config['source_image']);//썸네일 적용

		// ----- crop -----
		$image = WideImage::load($config['source_image']);
		$config['x_axis'] = 0;
		$config['y_axis'] = 0;
		if($_trigger == 'H') $config['y_axis'] = ($config['height'] - $target_height ) / 2; // 높이 조절
		else if($_trigger == 'W') $config['x_axis'] = ($config['width'] - $target_width ) / 2; // 넓이 조절
		$config['width'] = $target_width;
		$config['height'] = $target_height;
		$image->crop($config['x_axis'], $config['y_axis'], $config['width'], $config['height'])->saveToFile($config['source_image']);
	}
	else {

		$image = WideImage::load($config['source_image']);
		$config['x_axis'] = 0;
		$config['y_axis'] = 0;
		if($_mode == 'copy') { // 복사모드
			$config['source_name'] = $_thumb_w.'x'.$_thumb_h.'_'.$_thumb_img;
			$config['source_image'] = $_thumb_dir.$config['source_name'];
			@unlink($_thumb_dir.$config['source_image']); // 기존 이미지 삭제
		}
		if( $target_ratio > $src_ratio ) {
			$_trigger = 'H'; // 높이 조절함.
			$config['width'] = ceil($target_width);
			$config['height'] = ceil($src_height * $target_width / $src_width);
		}
		// 원본의 넓이 비율이 목표의 넓이 비율보다 클 경우 - 목표의 높이에 기준을 맞춤 - 그래야 자를 수 있는 여지가 생김
		else if( $target_ratio < $src_ratio ) {
			$_trigger = 'W'; // 넓이 조절함.
			$config['width'] = ceil($src_width * $target_height / $src_height);
			$config['height'] = ceil($target_height);
		}
		if($_trigger == 'H') $config['y_axis'] = ($config['height'] - $target_height ) / 2; // 높이 조절
		else if($_trigger == 'W') $config['x_axis'] = ($config['width'] - $target_width ) / 2; // 넓이 조절
		$config['width'] = $target_width;
		$config['height'] = $target_height;
		$image->crop($config['x_axis'], $config['y_axis'], $config['width'], $config['height'])->saveToFile($config['source_image']);
	}
	// ----- 크기가 다를 경우 적용 -----

	return $config;
}
// -------- 비율 썸네일 적용 (resize / crop) --------


# 2016-11-17 LDD 정축 기준으로 지정된 크기로 크롭
function ImgCrop($_thumb_dir, $_thumb_img, $_thumb_w, $_thumb_h){
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/wideimage/lib/WideImage.php');

	// 설정
	$config['source_image_old'] = $_thumb_dir.$_thumb_img;
	$config['source_image'] = $_thumb_dir.$_thumb_img;
	$config['source_path'] = $_thumb_dir;
	$config['source_name'] = $_thumb_img;
	$config['quality'] = 100;
	$image = WideImage::load($config['source_image']);
	$imageSize = getimagesize($config['source_image']);
	if($imageSize['width'] == $_thumb_w && $imageSize['height'] == $_thumb_h) return;
	$config['x_axis'] = ($imageSize['width']-$_thumb_w)/2;
	$config['y_axis'] = ($imageSize['height']-$_thumb_h)/2;
	$config['width'] = $_thumb_w;
	$config['height'] = $_thumb_h;
	$config['quality'] = 100;
	$image->crop($config['x_axis'], $config['y_axis'], $config['width'], $config['height'])->saveToFile($config['source_image']);
}