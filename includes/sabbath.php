<?php declare(strict_types=1);
/*
Keep Sabbath is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Keep Sabbath is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Keep Sabbath. If not, see https://www.gnu.org/licenses/.
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}


class Keep_Sabbath_DateTime {

    /**
     * Get the sunset time of the given $day at $lat and $lng
     *
     * @since 1.0.0
     *
     * @param DateTime $day
     * @param float $lat Latitude of the location
     * @param float $lng Longitude of the location
     * @return DateTime
     */
    public function get_sunset_time(DateTime $day, float $lat, float $lng) {
        // Get sun information
        $sun_info = date_sun_info($day->getTimestamp(), $lat, $lng);
    
        foreach ($sun_info as $key => $val) {
            $time = new DateTime('@' . $val);
            $time->setTimezone($day->getTimeZone());
            $sun_info[$key] = $time->format('m/d/Y h:i a');
        }
    
        $sunset_time = new DateTime($sun_info['sunset']);
        return $sunset_time;
    }
    

    /**
     * Get the number of the day of the week as a string.
     *
     * @since 1.0.0
     *
     * @param DateTime $today The current date and time
     * @return string A string integer of 0-6, where "Sunday" is 0
     */
    private function what_weekday(DateTime $today) {
        return $today->format('N');
    }
    

    /**
     * Whether it is before, during, or after the Biblical day. 
     * 
     * This is based on the understanding that the Biblical day goes from sunset 
     * to sunset. The day starts in the evening at sunset and ends at sunset the next day, 
     * unlike the 24-hour clock which uses calculations to determine the new day at midnight.
     * (See Genesis 1)
     *
     * @since 1.0.0
     *
     * @param DateTime $day_starts The evening before sunset time
     * @param DateTime $day_ends The actual day sunset time
     * @return boolean 
     */
    public function is_during_biblical_day(DateTime $day_starts, DateTime $day_ends) {
        // Right now
        $now = new DateTime();
    
        if ($day_starts < $now) {
            // We know the day has started
            if ($day_ends < $now) {
                // It is after the day now
                return false;
            } else {
                // It is still during the day
                return true;
            }
        } else if ($day_starts > $now) {
            // It is before the day
            return false;
        } else {
            // This should never happen... :)
            return false;
        }
    }
    

    /**
     * Get the beginning and ending times of the day (sunset to sunset).
     *
     * @since 1.0.0
     *
     * @param DateTime $prior_day The datetime day before, when the Biblical day begins at sunset
     * @param DateTime $day The datetime day that ends the Biblical day at sunset
     * @param float $lat Latitude of the location
     * @param float $lng Longitude of the location
     * @return array $args {
     *     @string begins => @DateTime datetime,
     *     @string ends => @DateTime datetime
     * }
     */
    public function get_day_begins_ends(DateTime $prior_day, DateTime $day, float $lat, float $lng) {
    
        // Calculate sunrise and sunset
        $prior_day_sunset = $this->get_sunset_time($prior_day, $lat, $lng);
        $day_sunset = $this->get_sunset_time($day, $lat, $lng);
        
        //echo "Day will begin at {$prior_day_sunset->format('m/d/Y h:i a')} ";
        //echo "Day will end at {$day_sunset->format('m/d/Y h:i a')} ";
    
        return array(
            'begins' => $prior_day_sunset,
            'ends' => $day_sunset
        );
    }
    

    /**
     * Calculate whether today is the Sabbath or not with the given $lat and $lng.
     * 
     * This should not be used directly. Use the more optimized is_sabbath method instead.
     *
     * @since 1.0.0
     *
     * @param string $weekday The current weekday
     * @param float $lat Latitude of the location
     * @param float $lng Longitude of the location
     * @return boolean 
     */
    private function get_is_sabbath(string $weekday, float $lat, float $lng) {
         $rev_sabbath_day = new DateTime();
         $sabbath_day = new DateTime();
     
         // The 6th day (rev Sabbath, the evening when the Sabbath starts).
         if ($weekday == '5') {
             $sabbath_day->add(new DateInterval('P1D'));
         // The 7th day (the Sabbath)
         } else if ($weekday == '6') {
             $rev_sabbath_day->sub(new DateInterval('P1D'));
         }
    
         $sabbath_times = $this->get_day_begins_ends($rev_sabbath_day, $sabbath_day, $lat, $lng);
         return $this->is_during_biblical_day($sabbath_times['begins'], $sabbath_times['ends']);
    }


    /**
     * Get whether the current day and time is the Sabbath or not.
     * 
     * This is based on an understanding that the Sabbath day starts at sundown Fri. and ends at sundown Sat. 
     *
     * @since 1.0.0
     *
     * @param float $lat Latitude of the location
     * @param float $lng Longitude of the location
     * @return boolean 
     */
    public function is_sabbath(float $lat, float $lng) {

        // See if it is currently the 6th or 7th day (Fri. or Sat.)
        $now = new DateTime();
        $weekday = $this->what_weekday($now);
    
        // This is a performance optimization:
        // It will only calculate the Sabbath times if it
        // is near the Sabbath day (rev-Shabbat) or the day-of.
        if ($weekday == '5' || $weekday == '6') {
            return $this->get_is_sabbath($weekday, $lat, $lng);
        } else {
            return $is_sabbath_value = false;
        }    
    }
    

    /**
     * Get whether the current day and time is a Holy Day in $holy_days.
     *
     * @since 1.0.0
     *
     * @param array<DateTime> $holy_days The holy days
     * @param float $lat Latitude of the location
     * @param float $lng Longitude of the location
     * @return boolean 
     */
    public function is_holy_day($holy_days, float $lat, float $lng) {
        foreach ($holy_days as $day) {
            $rev_day = clone $day;
            $rev_day->sub(new DateInterval('P1D'));
            $holy_day_times = $this->get_day_begins_ends($rev_day, $day, $lat, $lng);
            if ($this->is_during_biblical_day($holy_day_times['begins'], $holy_day_times['ends'])) {
                return true;
            }
        }
        return false;
    }
    
    
    /**
     * Get whether the current day and time is the Sabbath or a Holy day.
     * 
     * @since 1.0.0
     *
     * @param array<DateTime> $holy_days The holy days
     * @param float $lat Latitude of the location
     * @param float $lng Longitude of the location
     * @return boolean 
     */
    public function is_sabbath_or_holy_day($holy_days, float $lat, float $lng) {
        $is_sabbath_day = $this->is_sabbath($lat, $lng);

        if ($is_sabbath_day == true) {
            return true;
        } else {
            $is_holy_day = $this->is_holy_day($holy_days, $lat, $lng);
            return $is_holy_day;
        }
    }
}