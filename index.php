<?php
require 'includes/flights.php';

date_default_timezone_set("Asia/Manila");

// To simulate a live update of flights (you may change $current_date to see changes)
$current_date = "now"; // Format can be: Month Day, Year Hour:Min (e.g. Jan 1, 2026 5:00)
$currentTime = new DateTime($current_date, new DateTimeZone("Asia/Manila"));

$shiftDays = 2;

foreach ($flights as &$flight) {
    $depart = new DateTime($flight['depart_sched'], new DateTimeZone($flight['origin_TZ']));

    $hour = (int) $depart->format('H');
    $minute = (int) $depart->format('i');

    $newDepart = clone $currentTime;
    $newDepart->setTime($hour, $minute);
    $newDepart->modify("+{$shiftDays} days");

    $flight['depart_sched'] = $newDepart->format('M d, Y H:i');
}
unset($flight);

include 'includes/header.php'
    ?>

<div class="current-sched">
    Current Time: <?= $currentTime->format('l, M d, Y – h:i:s A'); ?>
</div>

<main>
    <div class="card-list">
        <?php foreach ($flights as $flight):
            $depart = new DateTime($flight['depart_sched'], new DateTimeZone($flight['origin_TZ']));

            $arrival = clone $depart;
            $arrival->add(new DateInterval('PT' . $flight['duration'] . 'M'));
            $arrival->setTimezone(new DateTimeZone($flight['dest_TZ']));

            $durationInterval = $depart->diff($arrival);
            $durationFormatted = $durationInterval->format('%h hr %i min');
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
                    Departure: <?= $depart->format('M d, Y – h:i A'); ?>
                </p>

                <p class="schedule">
                    Arrival: <?= $arrival->format('M d, Y – h:i A'); ?>
                </p>

                <p class="duration">
                    Duration: <?= $durationFormatted; ?>
                </p>
            </a>
        <?php endforeach; ?>
    </div>
</main>

<?php include 'includes/footer.php' ?>