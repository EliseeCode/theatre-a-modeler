<?php
// require_once "db.php";
// session_start();
// require_once "./Payments/config.php";
// $endpoint_secret = 'whsec_Zz4IwYANCal3EW6GS3tHnHNhCHbFxQTI';
// $payload = @file_get_contents('php://input');
// $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
// $event = null;
//
// try {
//     $event = \Stripe\Webhook::constructEvent(
//         $payload, $sig_header, $endpoint_secret
//     );
// } catch(\UnexpectedValueException $e) {
//     // Invalid payload
//     http_response_code(400);
//     exit();
// } catch(\Stripe\Exception\SignatureVerificationException $e) {
//     // Invalid signature
//     http_response_code(400);
//     exit();
// }
//
//  try {
//     $event = \Stripe\Event::constructFrom(
//         json_decode($payload, true)
//     );
// } catch(\UnexpectedValueException $e) {
//     // Invalid payload
//     http_response_code(400);
//     exit();
// }
//
// // Handle the event
// switch ($event->type) {
//     case 'customer.subscription.updated':
//       //$checkout = $event->data->object;
//     break;
//     case 'customer.subscription.deleted':
//       $sub_id = $event->data->object->id;
//       $date = date('m/d/Y h:i:s a', time());
//       $sql="UPDATE licence SET active=0,status='terminated',date_fin='".$date."' where sub_id='".$sub_id."'";
//       $mysqli->query($sql2);
//     break;
//     case 'checkout.session.completed':
//         $checkout = $event->data->object; // contains a \Stripe\PaymentIntent
//         $user_id=$checkout['client_reference_id'];
//         $plan_id=$checkout['display_items'][0]["plan"]["id"];
//         $subscription=$checkout['subscription'];
//         $amount=(int)$checkout['display_items'][0]["amount"];
//         $product_name=$checkout['display_items'][0]["plan"]["nickname"];
//         $currency=$checkout['display_items'][0]["currency"];
//         $currentTime=date("Y-m-d");
//         $role="SuperStudent";
//         if($product_name=="Super School"){$nbre_max=10;$role="SuperSchoolManager";}else{$nbre_max=1;}
//         if($product_name=="Super Teacher"){$role="SuperTeacher";}
//         if($product_name=="Super Student"){$role="SuperStudent";}
//         $sql="INSERT INTO `licences`(`licence_type`, `date_ini`,`status`, `nbre_max`, `sub_id`, `amount`, `currency`, `active`)
//         VALUES ('".$product_name."','".$currentTime."','running',".$nbre_max.",'".$subscription."',".$amount.",'".$currency."',1)";
//         $mysqli->query($sql);
//         $licence_id=$mysqli->insert_id;
//         //$licence_id=33;
//         $sql2="INSERT INTO user_licence (licence_id, user_id, licence_role,	licence_starting_date)
//         VALUES (".$licence_id.",".$user_id.",'".$role."','".$currentTime."')";
//         $mysqli->query($sql2);
//         file_put_contents('./Payments/exemple.txt', $event.'-----'.$sql."----".$sql2."--".$user_id.'-----'.$amount.'-----'.$product_name.'-----'.$currency.'-----'.$plan_id."----".$subscription);
//
//         break;
//     // case 'payment_method.attached':
//     //     $paymentMethod = $event->data->object; // contains a \Stripe\PaymentMethod
//     //     handlePaymentMethodAttached($paymentMethod);
//     //     break;
//     // ... handle other event types
//     default:
//         // Unexpected event type
//         http_response_code(400);
//         exit();
// }

http_response_code(200);
