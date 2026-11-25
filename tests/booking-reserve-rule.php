<?php
require __DIR__ . '/../public_html/admiralteyskaya/couch/addons/garden-booking/booking-lib.php';
$count = 0;
function check($condition, $label) {
    global $count;
    if (!$condition) throw new RuntimeException($label);
    $count++;
}
foreach (array('admiral' => '+79956246808', 'udelnaya' => '+79500473365') as $branch => $phone) {
    $rule = array('enabled'=>'1', 'days'=>'5,6', 'start'=>'18:00', 'end'=>'22:00', 'minutes'=>'90',
        'message'=>'Осталось меньше {minutes} минут.', 'phone'=>$phone);
    foreach (array(
        array('27.11.2026', '21:00', '2026-11-27T20:00:00+03:00', true),
        array('28.11.2026', '21:00', '2026-11-28T20:00:00+03:00', true),
        array('29.11.2026', '21:00', '2026-11-29T20:00:00+03:00', false),
        array('27.11.2026', '21:00', '2026-11-27T19:30:00+03:00', false),
        array('27.11.2026', '21:00', '2026-11-27T19:30:01+03:00', true),
        array('27.11.2026', '18:00', '2026-11-27T17:00:00+03:00', true),
        array('27.11.2026', '17:59', '2026-11-27T17:00:00+03:00', false),
        array('27.11.2026', '22:00', '2026-11-27T21:00:00+03:00', true),
        array('27.11.2026', '22:01', '2026-11-27T21:00:00+03:00', false),
        array('28.11.2026', '21:00', '2026-11-27T20:00:00+03:00', false),
        array('27.11.2026', '21:00', '2026-11-27T11:30:00-05:00', false),
    ) as $case) {
        $result = garden_booking_reserve_warning($rule, garden_booking_parse_visit($case[0], $case[1]), new DateTimeImmutable($case[2]));
        check(($result !== null) === $case[3], $branch . ' ' . implode(' ', $case));
        if ($result) {
            check($result['phone_href'] === 'tel:' . $phone, 'Branch phone');
            check(strpos($result['message'], '90') !== false, 'Message replacement');
        }
    }
    $visit = garden_booking_parse_visit('27.11.2026', '21:00');
    $now = new DateTimeImmutable('2026-11-27T20:00:00+03:00');
    $rule['enabled']='0';
    check(garden_booking_reserve_warning($rule,$visit,$now)===null,'Disabled');
    $rule['enabled']='1'; $rule['minutes']='60';
    check(garden_booking_reserve_warning($rule,$visit,$now)===null,'Custom lead time');
    $rule['days']='7';
    check(garden_booking_reserve_warning($rule,$visit,$now)===null,'Custom days');
    $rule['days']='5'; $rule['minutes']='120'; $rule['start']='20:00'; $rule['end']='02:00';
    check(garden_booking_reserve_warning($rule,$visit,$now)!==null,'Overnight interval');
}
foreach (array(array('31.02.2026','21:00'),array('27.11.2026','24:00'),array('27.11.2026','21:60'),array('27.11.2026',''),array("27.11.2026\0",'21:00')) as $bad) {
    check(garden_booking_parse_visit($bad[0],$bad[1])===null,'Invalid date/time');
}
echo "PASS: $count checks\n";
