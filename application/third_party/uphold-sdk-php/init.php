<?php
require(dirname(__FILE__) . '/lib/Uphold/Exception/UpholdClientException.php');
require(dirname(__FILE__) . '/lib/Uphold/Exception/AuthenticationRequiredException.php');
require(dirname(__FILE__) . '/lib/Uphold/Factory/UpholdClientFactory.php');
require(dirname(__FILE__) . '/lib/Uphold/HttpClient/HttpClientInterface.php');
require(dirname(__FILE__) . '/lib/Uphold/HttpClient/HttpClient.php');
//require(dirname(__FILE__) . '/lib/Uphold/HttpClient/Message/MessageFactory.php');

require(dirname(__FILE__) . '/lib/Uphold/Model/BaseModel.php');
require(dirname(__FILE__) . '/lib/Uphold/Model/RateInterface.php');
require(dirname(__FILE__) . '/lib/Uphold/Model/Rate.php');
require(dirname(__FILE__) . '/lib/Uphold/Model/ReserveInterface.php');
require(dirname(__FILE__) . '/lib/Uphold/Model/Reserve.php');
require(dirname(__FILE__) . '/lib/Uphold/Model/TransactionInterface.php');
require(dirname(__FILE__) . '/lib/Uphold/Model/Transaction.php');
require(dirname(__FILE__) . '/lib/Uphold/Model/UserInterface.php');
require(dirname(__FILE__) . '/lib/Uphold/Model/User.php');


require(dirname(__FILE__) . '/lib/Uphold/UpholdClient.php');