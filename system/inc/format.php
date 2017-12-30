<?php

/**
 * Appends a trailing slash.
 *
 * Will remove trailing forward and backslashes if it exists already before adding
 * a trailing forward slash. This prevents double slashing a string or path.
 *
 * The primary use of this is for paths and thus should be used for paths. It is
 * not restricted to paths and offers no specific path support.
 *
 * @since 1.2.0
 *
 * @param string $string What to add the trailing slash to.
 * @return string String with trailing slash added.
 */
function trailingslashit($string) {
    return untrailingslashit($string) . '/';
}

/**
 * Removes trailing forward slashes and backslashes if they exist.
 *
 * The primary use of this is for paths and thus should be used for paths. It is
 * not restricted to paths and offers no specific path support.
 *
 * @since 2.2.0
 *
 * @param string $string What to remove the trailing slashes from.
 * @return string String without the trailing slashes.
 */
function untrailingslashit($string) {
    return rtrim($string, '/\\');
}

/**
 * Convert Datetime to Words
 * @param string Timestamp
 * @return string Datetime are formatted
 */
function datetime_to_words_vi($timestamp) {
    $date = date("Y-m-d H:i:s");
    $now = new DateTime($date);
    $lastpost = date('Y-m-d H:i:s', $timestamp);
    $datetimeWrite = new DateTime($lastpost);
    $compareday = $now->diff($datetimeWrite);
    $days = $compareday->format('%a');

    $y = $compareday->format('%y');
    $m = $compareday->format('%m');
    $d = $compareday->format('%d');

    if ($d > 7 || $m > 0 || $y > 0) {
        return date('h:i:s d/m/Y', $timestamp);
    } else {
        $output = "";
        if ($d > 0) {
            if ($d == 7) {
                $output = "1 tuần ";
            } else {
                $output = "$d ngày ";
            }
        } else {
            $h = $compareday->format('%h');
            $i = $compareday->format('%i');
            $s = $compareday->format('%s');

            $hours = "";
            if ($h > 0) {
                $hours = "$h giờ, ";
            }

            $minutes = "";
            if ($i > 0) {
                $minutes = "$i phút, ";
            }

            $seconds = "";
            if ($s > 0) {
                $seconds = "$s giây ";
            } elseif (empty($hours) && empty($minutes)) {
                $seconds = "1 giây ";
            }

            $output = "$hours $minutes $seconds";
        }
        return " $output trước.";
    }
}

/**
 * Convert datetime from timestamp
 * @param int $timestamp Datetime format with timestamp, length = 10
 * @param string $format
 * @return string
 */
function format_datetime($timestamp, $format) {
    return $date = date($format, $timestamp);
}

//PARA: Date Should In YYYY-MM-DD Format
//RESULT FORMAT:
// '%y Year %m Month %d Day %h Hours %i Minute %s Seconds'        =>  1 Year 3 Month 14 Day 11 Hours 49 Minute 36 Seconds
// '%y Year %m Month %d Day'                                    =>  1 Year 3 Month 14 Days
// '%m Month %d Day'                                            =>  3 Month 14 Day
// '%d Day %h Hours'                                            =>  14 Day 11 Hours
// '%d Day'                                                        =>  14 Days
// '%h Hours %i Minute %s Seconds'                                =>  11 Hours 49 Minute 36 Seconds
// '%i Minute %s Seconds'                                        =>  49 Minute 36 Seconds
// '%h Hours                                                    =>  11 Hours
// '%a Days                                                        =>  468 Days
function dateDifference($date_1, $date_2, $differenceFormat = '%a') {
    $datetime1 = date_create($date_1);
    $datetime2 = date_create($date_2);

    $interval = date_diff($datetime1, $datetime2);

    return $interval->format($differenceFormat);
}
