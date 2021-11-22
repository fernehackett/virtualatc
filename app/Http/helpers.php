<?php

if (! function_exists('move_file')) {
    function move_file($file, $type='avatar', $withWatermark = false)
    {
        // Grab all variables
        $destinationPath = config('variables.'.$type.'.folder');
        \Log::info($destinationPath);
        \Log::info($file);
        $width           = config('variables.' . $type . '.width');
        $height          = config('variables.' . $type . '.height');
        $full_name       = Str::random(16) . '.' . $file->getClientOriginalExtension();

        if ($width == null && $height == null) { // Just move the file
            $a = $file->storeAs($destinationPath, $full_name);
            \Log::info(asset($a));
            return $full_name;
        }


        // Create the Image
        $image           = Image::make($file->getRealPath());

        if ($width == null || $height == null) {
            $image->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
        }else{
            $image->fit($width, $height);
        }

        if ($withWatermark) {
            $watermark = Image::make(public_path() . '/img/watermark.png')->resize($width * 0.5, null);

            $image->insert($watermark, 'center');
        }

        Storage::put($destinationPath . '/' . $full_name, (string) $image->encode());

        return $full_name;
    }
}
