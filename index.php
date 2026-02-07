<?php
// Author: Laxamana, Prince S.
// Section: WD203
// Date of Last Update: February 7, 2026

require 'includes/flights.php';

date_default_timezone_set("Asia/Manila");

// To simulate a live update of flights (you may change $current_date to see changes)
$current_date = "now"; // Format can be: Month Day, Year Hour:Min (e.g. Jan 1, 2026 5:00)
$current_time = new DateTime($current_date, new DateTimeZone("Asia/Manila"));

$add_days = 1; //simulate flight schedules (adding days later) 

foreach ($flights as &$flight) {

    //Usage of DateTime and DateTimeZone
    $depart = new DateTime($flight['depart_sched'], new DateTimeZone($flight['origin_TZ']));

    $hour = (int) $depart->format('H');
    $minute = (int) $depart->format('i');

    $newDepart = clone $current_time;
    $newDepart->setTime($hour, $minute);

    //Usage of modify
    $newDepart->modify("+{$add_days} days");

    $flight['depart_sched'] = $newDepart->format('M d, Y H:i');
}
unset($flight);

include 'includes/header.php'
    ?>

<div class="current-sched">
    Current Time: <?= $current_time->format('l, M d, Y – h:i:s A'); ?>
</div>

<main>
    <div class="card-list">
        <?php foreach ($flights as $flight):
            $depart = new DateTime($flight['depart_sched'], new DateTimeZone($flight['origin_TZ']));

            $arrival = clone $depart;

            //Usage of add
            $arrival->add(new DateInterval('PT' . $flight['duration'] . 'M'));
            $arrival->setTimezone(new DateTimeZone($flight['dest_TZ']));

            //Usage of diff
            $durationInterval = $depart->diff($arrival);
            $durationFormatted = $durationInterval->format('%h hr %i min');

            //bonus (Time before depart)
            if ($current_time > $depart) {
                $countdown = "00:00:00";
            } else {
                $timeBeforeDepart = $current_time->diff($depart);

                $hours = $timeBeforeDepart->h + ($timeBeforeDepart->days * 24);
                $minutes = $timeBeforeDepart->i;
                $seconds = $timeBeforeDepart->s;

                $countdown = "{$hours}:{$minutes}:{$seconds}";
            }
            ?>

            <a href="#" class="card-item flight-card">

                <img src="<?= $flight['image']; ?>" alt="<?= $flight['destination']; ?>">

                <span class="<?= strtolower($flight['type']); ?>">
                    <?= $flight['type']; ?>
                </span>

                <h3>
                    <?= $flight['origin']; ?> ✈ <?= $flight['destination']; ?>
                </h3>

                <p class="airline">
                    <?= $flight['airline']; ?> (<?= $flight['flight_num']; ?>)
                </p>

                <p class="schedule">
                    Departure: <?= $depart->format('M d, Y – h:i A') . " ({$depart->getTimezone()->getName()})"; ?>
                </p>

                <p class="schedule">
                    Arrival: <?= $arrival->format('M d, Y – h:i A') . " ({$arrival->getTimezone()->getName()})"; ?>
                </p>

                <p class="duration">
                    Duration: <?= $durationFormatted; ?>
                </p>

                <p class="countdown">
                    Departs in: <?= $countdown ?>
                </p>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Other timezones -->
    <div class="other-timezones">
        <?php
        $locations = [
            "Tokyo" => "Asia/Tokyo",
            "Dubai" => "Asia/Dubai",
            "Sydney" => "Australia/Sydney"
        ];
        ?>

        <?php foreach ($locations as $city => $tz): ?>
            <?php $date = clone $current_time;
            $date->setTimezone(new DateTimeZone($tz)); ?>
            <div>
                <div class="other-city">
                    <?= $city ?>
                </div>

                <div class="other-date-time">
                    <?= $date->format('M d, Y – h:i A') ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php include 'includes/footer.php' ?>