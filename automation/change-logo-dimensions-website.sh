#!/bin/bash
# Author: Andrew S via Upwork.com
# License: GPLv3

HEIGHT_BEFORE=50
WIDTH_BEFORE=109
HEIGHT_AFTER=50
WIDTH_AFTER=134

HEADER_BLADES=(
resources/views/user/signin_company.blade.php
resources/views/user/driver_cardetails.blade.php
resources/views/user/forgot_password.blade.php
resources/views/user/signup_company.blade.php
resources/views/user/signin_driver.blade.php
resources/views/user/signup_rider.blade.php
resources/views/user/signup_driver.blade.php
resources/views/user/signin_rider.blade.php
resources/views/user/reset_password.blade.php
resources/views/common/header.blade.php
)

for HEADER_FILE in ""${HEADER_BLADES[@]}""; do
    perl -i -p -e "s/width: ${WIDTH_BEFORE}px;height: ${HEIGHT_BEFORE}px/width: ${WIDTH_AFTER}px;height: ${HEIGHT_AFTER}px/g" $HEADER_FILE
    perl -i -p -e "s/width: ${WIDTH_BEFORE}px; height: ${HEIGHT_BEFORE}px/width: ${WIDTH_AFTER}px; height: ${HEIGHT_AFTER}px/g" $HEADER_FILE
done

exit 0

# public/css/common1.css
# 55:    width: 109px;
# 62:    width: 109px;
# 90:    width: 109px;

# resources/views/user/driver_cardetails.blade.php
# 9:            <img class="white_logo" src="{{ $logo_url }}" style="width: 109px;height: 50px;object-fit: contain;">

# resources/views/user/signin_driver.blade.php
# 15:      <img class="white_logo" src="{{ $logo_url }}" style="width: 109px;height: 50px;background-color: white;background-size: contain;">

# resources/views/user/signup_rider.blade.php
# 9:       <img class="white_logo" src="{{ $logo_url }}" style="width: 109px; height: 50px;background-size: contain;">

# resources/views/user/reset_password.blade.php
# 9:         <img class="white_logo" src="{{ $logo_url }}" style="width: 109px; height:50px;background-size: contain;">

# resources/views/user/signup_company.blade.php
# 19:         <img class="white_logo" src="{{ $logo_url }}" style="width: 109px; height: 50px;background-size: contain;">

# resources/views/user/signin_company.blade.php
# 15:      <img class="white_logo" src="{{ $logo_url }}" style="width: 109px;height: 50px;background-color: white;background-size: contain;">

# resources/views/user/signin_rider.blade.php
# 8:            <img class="white_logo" src="{{$logo_url }}" style="width: 109px;height:50px;background-size: contain;">

# resources/views/user/signup_driver.blade.php
# 19:         <img class="white_logo" src="{{ $logo_url }}" style="width: 109px; height: 50px;background-size: contain;">

# resources/views/user/forgot_password.blade.php
# 14:    width: 109px !important;

# resources/views/common/header.blade.php
# 9:        <a href="{{ url('/') }}"  class="pull-left logo-link"><img style="width: 109px;background-color: white;    margin-top: 15px;height: 50px !important;" src="{{ $logo_url }}"></a>
