CREATE DATABASE IF NOT EXISTS `taa_app`;

USE `taa_app`;

SET foreign_key_checks = 0;

DROP TABLE IF EXISTS `activity_log`;

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` varchar(255) NOT NULL DEFAULT 'none',
  `date` varchar(255) NOT NULL DEFAULT 'none',
  `status` varchar(255) NOT NULL DEFAULT 'none',
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1542 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `activity_log` VALUES (1250,"ADMIN: UPDATED OFFICIAL POSITION -  0401202511295839347 |  FROM CHAIRMANS TO CHAIRMAN","1-4-2025 1:05 PM","update"),
(1251,"ADMIN: DELETED POSITION -  77311317124789201092022180612765 | chairmans","1-4-2025 7:05 AM","delete"),
(1252,"ADMIN: ADDED RESIDENT - 23388956417195 |  Alexandra Kane Non commodi saepe se","1-4-2025 1:06 PM","create"),
(1253,"ADMIN: ADDED RESIDENT - 3188696235402 |  Alexandra Mooney In rem voluptatem E","1-4-2025 1:06 PM","create"),
(1254,"ADMIN: Admin Admin | LOGOUT","1-4-2025 7:07 AM","logout"),
(1255,"ADMIN: Admin Admin | LOGIN","1-4-2025 1:09 PM","login"),
(1256,"ADMIN: ADDED BLOTTER RECORD  -  3647560271426891 | Complainant - Alexandra Kane | Incident - Incident | Date Incident 2025-04-19T13:10 | Location Incident Location of Incident | Complainant Statement - Complainant Statement | Respondent - Respondent","1-4-2025 1:10 PM","delete"),
(1257,"ADMIN: ADDED BLOTTER RECORD  -  3647560271426891 | Person Involved - Alexandra Kane | Incident - Incident | Date Incident 2025-04-19T13:10 | Location Incident Location of Incident | Complainant Statement - Complainant Statement | Respondent - Respondent","1-4-2025 1:10 PM","delete"),
(1258,"ADMIN: ADDED BLOTTER RECORD  -  3647560271426891 | Person Not Resident - wq | Incident - Incident | Date Incident 2025-04-19T13:10 | Location Incident Location of Incident | Complainant Statement - ewqewq | Respondent - Respondent","1-4-2025 1:10 PM","delete"),
(1259,"ADMIN: ADDED BLOTTER RECORD  -  3647560271426891 | Complainant Not Resident - Complainant Not Resident | Incident - Incident | Date Incident 2025-04-19T13:10 | Location Incident Location of Incident | Complainant Statement - Complainant Statement | Respon","1-4-2025 1:10 PM","delete"),
(1260,"ADMIN: ADDED RESIDENT - 37404238492438 |  Miriam Frost Harum sit ut provide","1-4-2025 1:10 PM","create"),
(1261,"ADMIN: ADDED RESIDENT - 1086692484891 |  Jelani Ellison Dolorum qui qui id v","1-4-2025 1:11 PM","create"),
(1262,"ADMIN: ADDED RESIDENT - 12435095932673 |  Darrel Kline Quas perferendis aut","1-4-2025 1:11 PM","create"),
(1263,"ADMIN: ADDED RESIDENT - 34151365970057 |  Moana Burt Dolorum fugiat nisi","1-4-2025 1:11 PM","create"),
(1264,"ADMIN: ADDED OFFICIAL - 0401202513120186625 | KAGAWAD Branden Whitney Minim dolores velit | START 2021-12-17 END 1971-04-06","1-4-2025 1:12 PM","create"),
(1265,"ADMIN: Admin Admin | LOGOUT","1-4-2025 7:12 AM","logout"),
(1266,"ADMIN: Admin Admin | LOGIN","1-4-2025 1:15 PM","login"),
(1267,"ADMIN: Admin Admin | LOGOUT","29-10-2025 9:41 AM","logout"),
(1268,"ADMIN: Admin Admin | LOGIN","29-10-2025 4:43 PM","login"),
(1269,"ADMIN: Admin Admin | LOGOUT","29-10-2025 9:48 AM","logout"),
(1270,"ADMIN: Admin Admin | LOGIN","29-10-2025 4:55 PM","login"),
(1271,"ADMIN: Admin Admin | LOGOUT","29-10-2025 9:58 AM","logout"),
(1272,"ADMIN: Admin Admin | LOGIN","29-10-2025 4:58 PM","login"),
(1273,"ADMIN: Admin Admin | LOGOUT","29-10-2025 9:58 AM","logout"),
(1274,"ADMIN: Admin Admin | LOGIN","29-10-2025 4:58 PM","login"),
(1275,"ADMIN: DELETED POSITION -  268778674891281501142022025704271 | kagawad","29-10-2025 10:52 AM","delete"),
(1276,"ADMIN: DELETED POSITION -  811981911875128801142022163118246 | sk kagawad","29-10-2025 10:52 AM","delete"),
(1277,"ADMIN: UPDATED POSITION -  619131249471207208162022141229307 |  FROM chairman TO PRESIDENT","29-10-2025 10:54 AM","update"),
(1278,"ADMIN: ADDED POSITION -  124707950268138210292025175638337 |  POSITION NAME Vice president | POSITION LIMIT 1","29-10-2025 5:56 PM","added"),
(1279,"ADMIN: ADDED POSITION -  997314582282906410292025175650550 |  POSITION NAME DIRECTOR | POSITION LIMIT 1","29-10-2025 5:56 PM","added"),
(1280,"ADMIN: ADDED POSITION -  144837147199673010292025175706513 |  POSITION NAME TReASURER | POSITION LIMIT 1","29-10-2025 5:57 PM","added"),
(1281,"ADMIN: DELETED POSITION -  997314582282906410292025175650550 | DIRECTOR","29-10-2025 10:57 AM","delete"),
(1282,"ADMIN: ADDED POSITION -  717828409372790110292025175818344 |  POSITION NAME DIRECTOR | POSITION LIMIT 1","29-10-2025 5:58 PM","added"),
(1283,"ADMIN: ADDED POSITION -  891444888698007310292025175838820 |  POSITION NAME AUDITOR | POSITION LIMIT 1","29-10-2025 5:58 PM","added"),
(1284,"ADMIN: ADDED POSITION -  131780449283631910292025175851351 |  POSITION NAME SECRETARY | POSITION LIMIT 1","29-10-2025 5:58 PM","added"),
(1285,"ADMIN: Admin Admin | LOGOUT","29-10-2025 11:01 AM","logout"),
(1286,"OFFICIAL: Secretary Secretary | LOGIN","29-10-2025 6:01 PM","login"),
(1287,"OFFICIAL: Secretary Secretary | LOGOUT","29-10-2025 11:03 AM","logout"),
(1288,"OFFICIAL: Secretary Secretary | LOGIN","29-10-2025 6:03 PM","login"),
(1289,"OFFICIAL: Secretary Secretary | LOGOUT","29-10-2025 11:05 AM","logout"),
(1290,"ADMIN: Admin Admin | LOGIN","29-10-2025 6:05 PM","login"),
(1291,"ADMIN: UPDATED POSITION -  124707950268138210292025175638337 |  FROM Vice president TO VICE PRESIDENT","29-10-2025 11:06 AM","update"),
(1292,"ADMIN: UPDATED POSITION -  144837147199673010292025175706513 |  FROM TReASURER TO TREASURER","29-10-2025 11:06 AM","update"),
(1293,"ADMIN: ADDED RESIDENT - 96230707934287 |  Brian Paul Gabriel Bulahan ","29-10-2025 6:10 PM","create"),
(1294,"ADMIN: ADDED RESIDENT - 62636923746663 |  Maria Rubelina Paguio ","29-10-2025 6:12 PM","create"),
(1295,"ADMIN: ADDED OFFICIAL - 1029202518161844552 | DIRECTOR Aris Aguila  | START 2023-07-23 END 2025-07-23","29-10-2025 6:16 PM","create"),
(1296,"ADMIN: Admin Admin | LOGOUT","29-10-2025 11:22 AM","logout"),
(1297,"RESIDENT: Maria Rubelina Paguio | LOGIN","29-10-2025 6:22 PM","login"),
(1298,"RESIDENT: Maria Rubelina Paguio | LOGOUT","29-10-2025 11:22 AM","logout"),
(1299,"RESIDENT: Maria Rubelina Paguio | LOGIN","29-10-2025 6:23 PM","login"),
(1300,"RESIDENT: Maria Rubelina Paguio | LOGOUT","29-10-2025 11:26 AM","logout"),
(1301,"ADMIN: Admin Admin | LOGIN","29-10-2025 6:26 PM","login"),
(1302,"ADMIN: Admin Admin | LOGOUT","29-10-2025 11:26 AM","logout"),
(1303,"ADMIN: Admin Admin | LOGIN","29-10-2025 6:26 PM","login"),
(1304,"ADMIN: Admin Admin | LOGOUT","29-10-2025 11:26 AM","logout"),
(1305,"OFFICIAL: Secretary Secretary | LOGIN","29-10-2025 6:27 PM","login"),
(1306,"OFFICIAL: Secretary Secretary | LOGOUT","29-10-2025 11:31 AM","logout"),
(1307,"RESIDENT: Maria Rubelina Paguio | LOGIN","29-10-2025 6:31 PM","login"),
(1308,"RESIDENT: Maria Rubelina Paguio | LOGOUT","29-10-2025 11:35 AM","logout"),
(1309,"OFFICIAL: Secretary Secretary | LOGIN","29-10-2025 6:35 PM","login"),
(1310,"OFFICIAL: Secretary Secretary | LOGOUT","29-10-2025 1:46 PM","logout"),
(1311,"OFFICIAL: Secretary Secretary | LOGIN","29-10-2025 8:46 PM","login"),
(1312,"OFFICIAL: Secretary Secretary | LOGOUT","29-10-2025 1:46 PM","logout"),
(1313,"OFFICIAL: Secretary Secretary | LOGIN","29-10-2025 8:46 PM","login"),
(1314,"OFFICIAL: Secretary Secretary | LOGOUT","29-10-2025 1:46 PM","logout"),
(1315,"ADMIN: Admin Admin | LOGIN","29-10-2025 8:46 PM","login"),
(1316,"ADMIN: Admin Admin | LOGOUT","29-10-2025 1:49 PM","logout"),
(1317,"OFFICIAL: Secretary Secretary | LOGIN","29-10-2025 8:50 PM","login"),
(1318,"OFFICIAL: Secretary Secretary | LOGOUT","29-10-2025 1:50 PM","logout"),
(1319,"OFFICIAL: Secretary Secretary | LOGIN","29-10-2025 8:50 PM","login"),
(1320,"OFFICIAL: Secretary Secretary | LOGOUT","29-10-2025 1:50 PM","logout"),
(1321,"RESIDENT: Maria Rubelina Paguio | LOGIN","29-10-2025 8:51 PM","login"),
(1322,"RESIDENT: Maria Rubelina Paguio | LOGOUT","29-10-2025 1:51 PM","logout"),
(1323,"ADMIN: Admin Admin | LOGIN","29-10-2025 8:52 PM","login"),
(1324,"ADMIN: Admin Admin | LOGOUT","29-10-2025 1:52 PM","logout"),
(1325,"RESIDENT: Maria Rubelina Paguio | LOGIN","29-10-2025 8:52 PM","login"),
(1326,"RESIDENT - :   | REQUEST CERTIFICATE - ","29-10-2025 9:35 PM","create"),
(1327,"RESIDENT - :   | REQUEST CERTIFICATE - ","29-10-2025 9:36 PM","create"),
(1328,"RESIDENT - :   | REQUEST CERTIFICATE - ","29-10-2025 9:37 PM","create"),
(1329,"RESIDENT - :   | REQUEST CERTIFICATE - ","29-10-2025 9:38 PM","create"),
(1330,"RESIDENT - :   | REQUEST CERTIFICATE - ","29-10-2025 9:42 PM","create"),
(1331,"RESIDENT - :   | REQUEST CERTIFICATE - ","29-10-2025 9:42 PM","create"),
(1332,"RESIDENT - :   | REQUEST CERTIFICATE - ","30-10-2025 2:26 AM","create"),
(1333,"RESIDENT - :   | REQUEST CERTIFICATE - ","30-10-2025 2:26 AM","create"),
(1334,"RESIDENT: Maria Rubelina Paguio | LOGIN","30-10-2025 12:54 PM","login"),
(1335,"RESIDENT: Maria Rubelina Paguio | LOGOUT","30-10-2025 3:51 PM","logout"),
(1336,"OFFICIAL: Secretary Secretary | LOGIN","30-10-2025 10:51 PM","login"),
(1337,"OFFICIAL: Secretary Secretary | LOGOUT","30-10-2025 3:55 PM","logout"),
(1338,"ADMIN: Admin Admin | LOGIN","30-10-2025 10:55 PM","login"),
(1339,"ADMIN: Admin Admin | LOGOUT","30-10-2025 4:20 PM","logout"),
(1340,"ADMIN: Admin Admin | LOGIN","30-10-2025 11:20 PM","login"),
(1341,"ADMIN: Admin Admin | LOGOUT","30-10-2025 4:20 PM","logout"),
(1342,"ADMIN: Admin Admin | LOGIN","30-10-2025 11:27 PM","login"),
(1343,"ADMIN: Admin Admin | LOGOUT","30-10-2025 4:27 PM","logout"),
(1344,"OFFICIAL: Secretary Secretary | LOGIN","30-10-2025 11:28 PM","login"),
(1345,"OFFICIAL: Secretary Secretary | LOGOUT","30-10-2025 4:30 PM","logout"),
(1346,"ADMIN: Admin Admin | LOGIN","30-10-2025 11:30 PM","login"),
(1347,"ADMIN: Admin Admin | LOGOUT","30-10-2025 4:30 PM","logout"),
(1348,"RESIDENT: Maria Rubelina Paguio | LOGIN","30-10-2025 11:31 PM","login"),
(1349,"RESIDENT: Maria Rubelina Paguio | LOGOUT","30-10-2025 4:31 PM","logout"),
(1350,"ADMIN: Admin Admin | LOGIN","30-10-2025 11:31 PM","login"),
(1351,"ADMIN: Admin Admin | LOGOUT","30-10-2025 4:35 PM","logout"),
(1352,"OFFICIAL: Secretary Secretary | LOGIN","30-10-2025 11:35 PM","login"),
(1353,"OFFICIAL: Secretary Secretary | LOGOUT","30-10-2025 4:35 PM","logout"),
(1354,"ADMIN: Admin Admin | LOGIN","30-10-2025 11:35 PM","login"),
(1355,"ADMIN: Admin Admin | LOGOUT","30-10-2025 4:56 PM","logout"),
(1356,"OFFICIAL: Secretary Secretary | LOGIN","30-10-2025 11:56 PM","login"),
(1357,"OFFICIAL: Secretary Secretary | LOGOUT","30-10-2025 5:15 PM","logout"),
(1358,"ADMIN: Admin Admin | LOGIN","31-10-2025 12:15 AM","login"),
(1359,"ADMIN: Admin Admin | LOGOUT","30-10-2025 6:16 PM","logout"),
(1360,"OFFICIAL: Secretary Secretary | LOGIN","31-10-2025 1:17 AM","login"),
(1361,"OFFICIAL: Secretary Secretary | LOGOUT","30-10-2025 6:17 PM","logout"),
(1362,"RESIDENT: Maria Rubelina Paguio | LOGIN","31-10-2025 1:19 AM","login"),
(1363,"RESIDENT: Maria Rubelina Paguio | LOGOUT","30-10-2025 6:19 PM","logout"),
(1364,"ADMIN: Admin Admin | LOGIN","31-10-2025 1:19 AM","login"),
(1365,"ADMIN: Admin Admin | LOGOUT","30-10-2025 7:17 PM","logout"),
(1366,"OFFICIAL: Secretary Secretary | LOGIN","31-10-2025 2:17 AM","login"),
(1367,"OFFICIAL: Secretary Secretary | LOGOUT","30-10-2025 7:21 PM","logout"),
(1368,"RESIDENT: Maria Rubelina Paguio | LOGIN","31-10-2025 2:21 AM","login"),
(1369,"RESIDENT: Maria Rubelina Paguio | LOGOUT","30-10-2025 7:25 PM","logout"),
(1370,"RESIDENT: Maria Rubelina Paguio | LOGIN","31-10-2025 2:26 AM","login"),
(1371,"RESIDENT: Maria Rubelina Paguio | LOGOUT","30-10-2025 7:27 PM","logout"),
(1372,"RESIDENT: Maria Rubelina Paguio | LOGIN","31-10-2025 2:37 AM","login"),
(1373,"RESIDENT: Maria Rubelina Paguio | LOGOUT","30-10-2025 7:37 PM","logout"),
(1374,"ADMIN: Admin Admin | LOGIN","31-10-2025 2:39 AM","login"),
(1375,"ADMIN: Admin Admin | LOGOUT","30-10-2025 7:40 PM","logout"),
(1376,"OFFICIAL: Secretary Secretary | LOGIN","31-10-2025 2:40 AM","login"),
(1377,"OFFICIAL: Secretary Secretary | LOGOUT","30-10-2025 7:41 PM","logout"),
(1378,"ADMIN: Admin Admin | LOGIN","31-10-2025 2:41 AM","login"),
(1379,"ADMIN: Admin Admin | LOGOUT","30-10-2025 7:44 PM","logout"),
(1380,"OFFICIAL: Secretary Secretary | LOGIN","31-10-2025 2:44 AM","login"),
(1381,"OFFICIAL: Secretary Secretary | LOGOUT","30-10-2025 7:46 PM","logout"),
(1382,"ADMIN: Admin Admin | LOGIN","31-10-2025 2:46 AM","login"),
(1383,"ADMIN: Admin Admin | LOGIN","31-10-2025 11:58 AM","login"),
(1384,"ADMIN: Admin Admin | LOGOUT","31-10-2025 4:58 AM","logout"),
(1385,"OFFICIAL: Secretary Secretary | LOGIN","31-10-2025 11:58 AM","login"),
(1386,"OFFICIAL: Secretary Secretary | LOGOUT","31-10-2025 12:55 PM","logout"),
(1387,"ADMIN: Admin Admin | LOGIN","31-10-2025 7:55 PM","login"),
(1388,"ADMIN: Admin Admin | LOGOUT","31-10-2025 1:00 PM","logout"),
(1389,"RESIDENT: Maria Rubelina Paguio | LOGIN","31-10-2025 8:00 PM","login"),
(1390,"RESIDENT: Maria Rubelina Paguio | LOGOUT","31-10-2025 1:00 PM","logout"),
(1391,"OFFICIAL: Secretary Secretary | LOGIN","31-10-2025 8:01 PM","login"),
(1392,"OFFICIAL: Secretary Secretary | LOGOUT","31-10-2025 1:24 PM","logout"),
(1393,"ADMIN: Admin Admin | LOGIN","31-10-2025 8:24 PM","login"),
(1394,"ADMIN: Admin Admin | LOGOUT","31-10-2025 1:26 PM","logout"),
(1395,"OFFICIAL: Secretary Secretary | LOGIN","31-10-2025 8:26 PM","login"),
(1396,"OFFICIAL: Secretary Secretary | LOGOUT","31-10-2025 1:28 PM","logout"),
(1397,"ADMIN: Admin Admin | LOGIN","31-10-2025 8:29 PM","login"),
(1398,"ADMIN: Admin Admin | LOGOUT","31-10-2025 1:29 PM","logout"),
(1399,"OFFICIAL: Secretary Secretary | LOGIN","31-10-2025 8:29 PM","login"),
(1400,"OFFICIAL: Secretary Secretary | LOGOUT","31-10-2025 1:35 PM","logout"),
(1401,"ADMIN: Admin Admin | LOGIN","31-10-2025 8:35 PM","login"),
(1402,"ADMIN: Admin Admin | LOGOUT","31-10-2025 1:35 PM","logout"),
(1403,"ADMIN: Admin Admin | LOGIN","31-10-2025 8:35 PM","login"),
(1404,"ADMIN: Admin Admin | LOGOUT","31-10-2025 1:36 PM","logout"),
(1405,"ADMIN: Admin Admin | LOGIN","31-10-2025 9:15 PM","login"),
(1406,"ADMIN: Admin Admin | LOGOUT","31-10-2025 6:15 PM","logout"),
(1407,"RESIDENT: Maria Rubelina Paguio | LOGIN","1-11-2025 1:23 AM","login"),
(1408,"RESIDENT: Maria Rubelina Paguio | LOGOUT","31-10-2025 6:24 PM","logout"),
(1409,"OFFICIAL: Secretary Secretary | LOGIN","1-11-2025 1:24 AM","login"),
(1410,"OFFICIAL: Secretary Secretary | LOGOUT","31-10-2025 6:54 PM","logout"),
(1411,"ADMIN: Admin Admin | LOGIN","1-11-2025 1:54 AM","login"),
(1412,"ADMIN: Admin Admin | LOGOUT","31-10-2025 6:56 PM","logout"),
(1413,"OFFICIAL: Secretary Secretary | LOGIN","1-11-2025 1:56 AM","login"),
(1414,"OFFICIAL: Secretary Secretary | LOGOUT","31-10-2025 6:57 PM","logout"),
(1415,"OFFICIAL: Secretary Secretary | LOGIN","1-11-2025 1:57 AM","login"),
(1416,"OFFICIAL: Secretary Secretary | LOGOUT","31-10-2025 7:54 PM","logout"),
(1417,"ADMIN: Admin Admin | LOGIN","1-11-2025 2:54 AM","login"),
(1418,"ADMIN: Admin Admin | LOGOUT","31-10-2025 7:54 PM","logout"),
(1419,"OFFICIAL: Secretary Secretary | LOGIN","1-11-2025 2:54 AM","login"),
(1420,"OFFICIAL: Secretary Secretary | LOGOUT","31-10-2025 8:14 PM","logout"),
(1421,"ADMIN: Admin Admin | LOGIN","1-11-2025 3:14 AM","login"),
(1422,"ADMIN: Admin Admin | LOGOUT","31-10-2025 8:14 PM","logout"),
(1423,"OFFICIAL: Secretary Secretary | LOGIN","1-11-2025 3:15 AM","login"),
(1424,"OFFICIAL: Secretary Secretary | LOGIN","1-11-2025 2:51 PM","login"),
(1425,"OFFICIAL: Secretary Secretary | LOGOUT","1-11-2025 8:04 AM","logout"),
(1426,"OFFICIAL: Secretary Secretary | LOGIN","1-11-2025 3:04 PM","login"),
(1427,"OFFICIAL: Secretary Secretary | LOGOUT","1-11-2025 8:18 AM","logout"),
(1428,"OFFICIAL: Secretary Secretary | LOGIN","2-11-2025 1:34 AM","login"),
(1429,"OFFICIAL: Secretary Secretary | LOGOUT","1-11-2025 7:19 PM","logout"),
(1430,"ADMIN: Admin Admin | LOGIN","2-11-2025 2:19 AM","login"),
(1431,"ADMIN: Admin Admin | LOGOUT","1-11-2025 7:19 PM","logout"),
(1432,"RESIDENT: Maria Rubelina Paguio | LOGIN","2-11-2025 2:19 AM","login"),
(1433,"RESIDENT: Maria Rubelina Paguio | LOGOUT","1-11-2025 7:20 PM","logout"),
(1434,"OFFICIAL: Secretary Secretary | LOGIN","2-11-2025 2:20 AM","login"),
(1435,"OFFICIAL: Secretary Secretary | LOGOUT","1-11-2025 7:20 PM","logout"),
(1436,"ADMIN: Admin Admin | LOGIN","2-11-2025 2:20 AM","login"),
(1437,"ADMIN: Admin Admin | LOGOUT","1-11-2025 7:21 PM","logout"),
(1438,"OFFICIAL: Secretary Secretary | LOGIN","2-11-2025 2:21 AM","login"),
(1439,"OFFICIAL: Secretary Secretary | LOGOUT","1-11-2025 7:21 PM","logout"),
(1440,"OFFICIAL: Secretary Secretary | LOGIN","2-11-2025 2:21 AM","login"),
(1441,"OFFICIAL: Secretary Secretary | LOGOUT","1-11-2025 7:21 PM","logout"),
(1442,"ADMIN: Admin Admin | LOGIN","2-11-2025 2:21 AM","login"),
(1443,"ADMIN: Admin Admin | LOGOUT","1-11-2025 7:21 PM","logout"),
(1444,"OFFICIAL: Secretary Secretary | LOGIN","2-11-2025 2:25 AM","login"),
(1445,"OFFICIAL: Secretary Secretary | LOGOUT","1-11-2025 7:28 PM","logout"),
(1446,"ADMIN: Admin Admin | LOGIN","2-11-2025 2:30 AM","login"),
(1447,"ADMIN: Admin Admin | LOGOUT","1-11-2025 7:30 PM","logout"),
(1448,"ADMIN: Admin Admin | LOGIN","2-11-2025 2:31 AM","login"),
(1449,"ADMIN: Admin Admin | LOGOUT","1-11-2025 7:32 PM","logout"),
(1450,"ADMIN: Admin Admin | LOGIN","2-11-2025 2:37 AM","login"),
(1451,"ADMIN: Admin Admin | LOGOUT","1-11-2025 7:39 PM","logout"),
(1452,"OFFICIAL: Secretary Secretary | LOGIN","2-11-2025 2:39 AM","login"),
(1453,"OFFICIAL: Secretary Secretary | LOGOUT","1-11-2025 7:40 PM","logout"),
(1454,"RESIDENT: Maria Rubelina Paguio | LOGIN","2-11-2025 2:41 AM","login"),
(1455,"RESIDENT: Maria Rubelina Paguio | LOGOUT","1-11-2025 7:42 PM","logout"),
(1456,"ADMIN: Admin Admin | LOGIN","2-11-2025 2:42 AM","login"),
(1457,"ADMIN: Admin Admin | LOGOUT","1-11-2025 7:42 PM","logout"),
(1458,"RESIDENT: Maria Rubelina Paguio | LOGIN","2-11-2025 2:43 AM","login"),
(1459,"RESIDENT: Maria Rubelina Paguio | LOGIN","2-11-2025 3:06 AM","login"),
(1460,"RESIDENT: Maria Rubelina Paguio | LOGOUT","1-11-2025 8:07 PM","logout"),
(1461,"ADMIN: Admin Admin | LOGIN","2-11-2025 3:07 AM","login"),
(1462,"ADMIN: Admin Admin | LOGOUT","1-11-2025 8:07 PM","logout"),
(1463,"RESIDENT: Maria Rubelina Paguio | LOGIN","2-11-2025 9:02 PM","login"),
(1464,"RESIDENT: Maria Rubelina Paguio | LOGOUT","2-11-2025 4:19 PM","logout"),
(1465,"RESIDENT: Maria Rubelina Paguio | LOGIN","2-11-2025 11:19 PM","login"),
(1466,"RESIDENT: Maria Rubelina Paguio | LOGOUT","2-11-2025 4:19 PM","logout"),
(1467,"RESIDENT: Maria Rubelina Paguio | LOGIN","2-11-2025 11:19 PM","login"),
(1468,"RESIDENT: Maria Rubelina Paguio | LOGOUT","2-11-2025 4:31 PM","logout"),
(1469,"OFFICIAL: Secretary Secretary | LOGIN","2-11-2025 11:32 PM","login"),
(1470,"OFFICIAL: Secretary Secretary | LOGOUT","2-11-2025 4:47 PM","logout"),
(1471,"ADMIN: Admin Admin | LOGIN","2-11-2025 11:47 PM","login"),
(1472,"ADMIN: Admin Admin | LOGOUT","2-11-2025 5:15 PM","logout"),
(1473,"RESIDENT: Maria Rubelina Paguio | LOGIN","3-11-2025 12:15 AM","login"),
(1474,"RESIDENT: Maria Rubelina Paguio | LOGOUT","2-11-2025 5:15 PM","logout"),
(1475,"ADMIN: Admin Admin | LOGIN","3-11-2025 1:49 AM","login"),
(1476,"ADMIN: Admin Admin | LOGOUT","2-11-2025 7:04 PM","logout"),
(1477,"RESIDENT: Maria Rubelina Paguio | LOGIN","3-11-2025 2:05 AM","login"),
(1478,"RESIDENT: Maria Rubelina Paguio | LOGOUT","2-11-2025 7:11 PM","logout"),
(1479,"OFFICIAL: Secretary Secretary | LOGIN","3-11-2025 2:11 AM","login"),
(1480,"OFFICIAL: Secretary Secretary | LOGOUT","2-11-2025 7:17 PM","logout"),
(1481,"RESIDENT: Maria Rubelina Paguio | LOGIN","3-11-2025 2:17 AM","login"),
(1482,"RESIDENT: Maria Rubelina Paguio | LOGOUT","2-11-2025 7:23 PM","logout"),
(1483,"OFFICIAL: Secretary Secretary | LOGIN","3-11-2025 2:23 AM","login"),
(1484,"OFFICIAL: Secretary Secretary | LOGOUT","2-11-2025 8:23 PM","logout"),
(1485,"ADMIN: Admin Admin | LOGIN","3-11-2025 3:23 AM","login"),
(1486,"ADMIN: Admin Admin | LOGOUT","2-11-2025 8:24 PM","logout"),
(1487,"RESIDENT: Maria Rubelina Paguio | LOGIN","3-11-2025 3:24 AM","login"),
(1488,"RESIDENT: Maria Rubelina Paguio | LOGOUT","2-11-2025 8:24 PM","logout"),
(1489,"ADMIN: Admin Admin | LOGIN","3-11-2025 11:28 AM","login"),
(1490,"ADMIN: Admin Admin | LOGIN","3-11-2025 2:20 PM","login"),
(1491,"ADMIN: Admin Admin | LOGOUT","3-11-2025 10:48 AM","logout"),
(1492,"RESIDENT: Maria Rubelina Paguio | LOGIN","3-11-2025 5:48 PM","login"),
(1493,"RESIDENT: Maria Rubelina Paguio | LOGOUT","3-11-2025 11:17 AM","logout"),
(1494,"RESIDENT: Maria Rubelina Paguio | LOGIN","3-11-2025 6:17 PM","login"),
(1495,"RESIDENT: Maria Rubelina Paguio | LOGOUT","3-11-2025 11:58 AM","logout"),
(1496,"OFFICIAL: Secretary Secretary | LOGIN","3-11-2025 8:15 PM","login"),
(1497,"OFFICIAL: Secretary Secretary | LOGOUT","3-11-2025 1:16 PM","logout"),
(1498,"OFFICIAL: Secretary Secretary | LOGIN","3-11-2025 8:16 PM","login"),
(1499,"OFFICAL: Secretary Secretary - 174668789044820710152022021619941 | UPDATED RESIDENT  STATUS - 62636923746663 |  FROM ACTIVE TO INACTIVE","3-11-2025 8:59 PM","update"),
(1500,"OFFICAL: Secretary Secretary - 174668789044820710152022021619941 | UPDATED RESIDENT  STATUS - 62636923746663 |  FROM INACTIVE TO ACTIVE","3-11-2025 8:59 PM","update"),
(1501,"OFFICIAL: Secretary Secretary | LOGOUT","3-11-2025 9:01 PM","logout"),
(1502,"RESIDENT: Maria Rubelina Paguio | LOGIN","4-11-2025 4:01 AM","login"),
(1503,"RESIDENT: Maria Rubelina Paguio | LOGOUT","3-11-2025 9:04 PM","logout"),
(1504,"ADMIN: Admin Admin | LOGIN","4-11-2025 4:04 AM","login"),
(1505,"ADMIN: Admin Admin | LOGOUT","3-11-2025 9:04 PM","logout"),
(1506,"ADMIN: Admin Admin | LOGIN","4-11-2025 4:04 AM","login"),
(1507,"ADMIN: Admin Admin | LOGIN","4-11-2025 11:58 PM","login"),
(1508,"ADMIN: Admin Admin | LOGIN","5-11-2025 8:20 PM","login"),
(1509,"ADMIN: Admin Admin | LOGOUT","5-11-2025 1:21 PM","logout"),
(1510,"OFFICIAL: Secretary Secretary | LOGIN","5-11-2025 8:21 PM","login"),
(1511,"OFFICIAL: Secretary Secretary | LOGOUT","5-11-2025 1:21 PM","logout"),
(1512,"RESIDENT: Maria Rubelina Paguio | LOGIN","5-11-2025 8:21 PM","login"),
(1513,"RESIDENT: Maria Rubelina Paguio | LOGOUT","5-11-2025 1:21 PM","logout"),
(1514,"ADMIN: Admin Admin | LOGIN","5-11-2025 8:22 PM","login"),
(1515,"ADMIN: Admin Admin | LOGOUT","5-11-2025 1:22 PM","logout"),
(1516,"OFFICIAL: Secretary Secretary | LOGIN","5-11-2025 8:22 PM","login"),
(1517,"OFFICIAL: Secretary Secretary | LOGOUT","5-11-2025 1:23 PM","logout"),
(1518,"ADMIN: Admin Admin | LOGIN","5-11-2025 8:23 PM","login"),
(1519,"ADMIN: Admin Admin | LOGOUT","5-11-2025 3:30 PM","logout"),
(1520,"RESIDENT: Maria Rubelina Paguio | LOGIN","5-11-2025 10:43 PM","login"),
(1521,"RESIDENT: Maria Rubelina Paguio | LOGOUT","5-11-2025 3:47 PM","logout"),
(1522,"ADMIN: Admin Admin | LOGIN","5-11-2025 10:47 PM","login"),
(1523,"ADMIN: Admin Admin | LOGOUT","5-11-2025 3:50 PM","logout"),
(1524,"RESIDENT: Maria Rubelina Paguio | LOGIN","5-11-2025 10:50 PM","login"),
(1525,"RESIDENT: Maria Rubelina Paguio | LOGOUT","5-11-2025 4:08 PM","logout"),
(1526,"OFFICIAL: Secretary Secretary | LOGIN","5-11-2025 11:09 PM","login"),
(1527,"OFFICAL: Secretary Secretary - 174668789044820710152022021619941 | DELETED RESIDENT -  62636923746663 |  - Maria Rubelina Paguio","5-11-2025 4:12 PM","update"),
(1528,"OFFICAL: Secretary Secretary - 174668789044820710152022021619941 | UNDELETED RESIDENT  - 62636923746663 | Maria Rubelina Paguio","5-11-2025 4:13 PM","delete"),
(1529,"OFFICIAL: Secretary Secretary | LOGOUT","5-11-2025 4:32 PM","logout"),
(1530,"RESIDENT: Maria Rubelina Paguio | LOGIN","5-11-2025 11:33 PM","login"),
(1531,"RESIDENT: Maria Rubelina Paguio | LOGOUT","5-11-2025 4:33 PM","logout"),
(1532,"OFFICIAL: Secretary Secretary | LOGIN","5-11-2025 11:33 PM","login"),
(1533,"OFFICIAL: Secretary Secretary | LOGOUT","5-11-2025 4:35 PM","logout"),
(1534,"RESIDENT: Maria Rubelina Paguio | LOGIN","5-11-2025 11:35 PM","login"),
(1535,"RESIDENT: Maria Rubelina Paguio | LOGOUT","5-11-2025 4:42 PM","logout"),
(1536,"OFFICIAL: Secretary Secretary | LOGIN","5-11-2025 11:42 PM","login"),
(1537,"OFFICIAL: Secretary Secretary | LOGOUT","5-11-2025 4:43 PM","logout"),
(1538,"ADMIN: Admin Admin | LOGIN","5-11-2025 11:43 PM","login"),
(1539,"ADMIN: ADDED POSITION -  804967351385394411052025234739394 |  POSITION NAME joker | POSITION LIMIT 1","5-11-2025 11:47 PM","added"),
(1540,"ADMIN: Admin Admin | LOGOUT","5-11-2025 5:03 PM","logout"),
(1541,"ADMIN: Admin Admin | LOGIN","6-11-2025 12:03 AM","login");


DROP TABLE IF EXISTS `announcements`;

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `date_posted` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `announcements` VALUES (1,"Annual Car Sticker","Message the secretary for your new car stickers. Avail for annual renewal every February.","2025-10-30 19:33:25"),
(2,"Garabe Collection","The garabe collections are on Tuesdays and Fridays from 8 AM to 12 PM.","2025-10-30 19:33:25"),
(3,"Free Pet Vaccination","Residents are invited for free pet vaccinations on October 30 at the basketball court.","2025-10-30 19:33:25"),
(4,"Monthly Dues","Residents are expected to pay association fees every month or quarter. Please message the secretary.","2025-10-30 19:33:25"),
(5,"eqeqwqer","werqwerqwr","2025-11-05 23:32:54");


DROP TABLE IF EXISTS `backup`;

CREATE TABLE `backup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=172 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `backup` VALUES (170,"BackupFile-04012025_071908.sql"),
(171,"BackupFile-11052025_170612.sql");


DROP TABLE IF EXISTS `barangay_information`;

CREATE TABLE `barangay_information` (
  `id` varchar(255) NOT NULL,
  `address` varchar(69) NOT NULL DEFAULT 'none',
  `postal_address` varchar(255) NOT NULL DEFAULT 'none',
  `image` varchar(255) NOT NULL DEFAULT 'none',
  `image_path` varchar(255) NOT NULL DEFAULT 'none',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `barangay_information` VALUES (32432432432432432,"Antipolo","Postal Address","Lowgo.png","../assets/dist/img/Lowgo.png");


DROP TABLE IF EXISTS `carousel`;

CREATE TABLE `carousel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `banner_title` varchar(255) NOT NULL,
  `banner_image` varchar(255) NOT NULL,
  `banner_image_path` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



DROP TABLE IF EXISTS `certificate`;

CREATE TABLE `certificate` (
  `a_i` int(11) NOT NULL AUTO_INCREMENT,
  `certificate_id` varchar(255) NOT NULL,
  `residence_id` varchar(255) NOT NULL,
  `certificate` varchar(255) NOT NULL,
  `ctc` varchar(255) NOT NULL,
  `issued_at` varchar(255) NOT NULL,
  `or_no` varchar(255) NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `control_no` varchar(255) NOT NULL,
  `created_at` varchar(255) NOT NULL,
  `expired_at` varchar(255) NOT NULL,
  PRIMARY KEY (`a_i`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



DROP TABLE IF EXISTS `certificate_request`;

CREATE TABLE `certificate_request` (
  `a_i` int(11) NOT NULL AUTO_INCREMENT,
  `id` varchar(255) NOT NULL,
  `residence_id` varchar(255) NOT NULL,
  `certificate_type` varchar(255) NOT NULL DEFAULT 'none',
  `purpose` varchar(255) NOT NULL DEFAULT 'none',
  `message` varchar(255) NOT NULL DEFAULT 'none',
  `date_issued` varchar(255) NOT NULL DEFAULT 'none',
  `date_request` varchar(255) NOT NULL DEFAULT 'none',
  `date_expired` varchar(255) NOT NULL DEFAULT 'none',
  `status` varchar(255) NOT NULL DEFAULT 'none',
  UNIQUE KEY `a_i` (`a_i`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `certificate_request` VALUES (64,"5073780181029202521354322319317406706902182f36afa","","none","","none","","10/29/2025","","PENDING"),
(65,"2141127496102920252136048132402060269021844c6a9b","","none","","none","","10/29/2025","","PENDING"),
(66,"2238491511029202521375297964187741690218b0ef38d","","none","","none","","10/29/2025","","PENDING"),
(67,"1627129867102920252138094411655418587690218c16bd30","","none","","none","","10/29/2025","","PENDING"),
(68,"161073367510292025214229408632823962690219c563c38","","none","","none","","10/29/2025","","PENDING"),
(69,"1956092511102920252142311501982800980690219c724d31","","none","","none","","10/29/2025","","PENDING"),
(70,"10474401211030202502262814257212760469025c5422dc6","","none","","none","","10/30/2025","","PENDING"),
(71,"129562492710302025022651467176530413069025c6b7229a","","none","","none","","10/30/2025","","PENDING");


DROP TABLE IF EXISTS `house_holds`;

CREATE TABLE `house_holds` (
  `a_i` int(11) NOT NULL AUTO_INCREMENT,
  `house_hold_id` varchar(255) NOT NULL DEFAULT 'none',
  `hold_unique` varchar(255) NOT NULL,
  `purok_id` varchar(255) NOT NULL,
  `residence_id` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `birth_date` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `educ_attainment` varchar(255) NOT NULL,
  `occupation` varchar(255) NOT NULL,
  `nawasa` varchar(255) NOT NULL,
  `water_pump` varchar(255) NOT NULL,
  `water_sealed` varchar(255) NOT NULL,
  `flush` varchar(255) NOT NULL,
  `religion` varchar(255) NOT NULL,
  `ethnicity` varchar(255) NOT NULL,
  `sangkap_seal` varchar(255) NOT NULL,
  `is_approved` varchar(255) NOT NULL,
  `is_resident` varchar(255) NOT NULL,
  PRIMARY KEY (`a_i`)
) ENGINE=InnoDB AUTO_INCREMENT=125 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `house_holds` VALUES (119,765720104206,90635228440419,916259339179300507242022155033612,16455182440138," First name","Middle name","Last name","2022-10-08","Female","educ","Occupation","YES","YES","YES","YES","Religion","Ethnicity","YES","APPROVED","NO"),
(120,377220153950,90635228440419,916259339179300507242022155033612,16455182440138,"qe","qwe","qweqwe","2022-10-12","Male","qweqw","wqe","YES","YES","YES","YES","qwe","eqwe","YES","APPROVED","NO"),
(121,377220153950,90635228440419,916259339179300507242022155033612,16455182440138,"First Name","Middle Name","Last Name","2022-10-27","Male","qwewqe","Occupation","YES","YES","YES","NO","Religion","qwe","YES","APPROVED","YES"),
(122,530011579769,70838125253292,916259339179300507242022155033612,54278971251733," First name","Middle name","Last name","2022-10-08","Male","Educational Attainment","Occupation","YES","YES","YES","YES","Religion","Ethnicity","YES","PENDING","NO"),
(123,85351248693,70838125253292,916259339179300507242022155033612,54278971251733,"qwe","wqe","wqewqe","2022-10-15","Female","wqe","wqe","YES","YES","YES","YES","qwe","qwe","YES","PENDING","NO"),
(124,85351248693,70838125253292,916259339179300507242022155033612,54278971251733,"Eugine","Palce","ROsillon","1997-09-06","Male","Educational Attainment","Wala","YES","YES","YES","YES","Catholic","Ethnicity","YES","PENDING","YES");


DROP TABLE IF EXISTS `incident_complainant`;

CREATE TABLE `incident_complainant` (
  `id` varchar(255) NOT NULL,
  `incident_main` varchar(255) NOT NULL,
  `complainant_id` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



DROP TABLE IF EXISTS `incident_record`;

CREATE TABLE `incident_record` (
  `incidentlog_id` varchar(255) NOT NULL,
  `complainant_not_residence` varchar(255) NOT NULL DEFAULT 'none',
  `statement` varchar(255) NOT NULL DEFAULT 'none',
  `respodent` varchar(255) NOT NULL DEFAULT 'none',
  `involved_not_resident` varchar(255) NOT NULL DEFAULT 'none',
  `statement_person` varchar(255) NOT NULL DEFAULT 'none',
  `date_incident` varchar(255) NOT NULL DEFAULT 'none',
  `date_reported` varchar(255) NOT NULL DEFAULT 'none',
  `type_of_incident` varchar(255) NOT NULL DEFAULT 'none',
  `location_incident` varchar(255) NOT NULL DEFAULT 'none',
  `status` varchar(69) NOT NULL DEFAULT 'none',
  `remarks` varchar(69) NOT NULL DEFAULT 'none',
  `date_added` varchar(255) NOT NULL DEFAULT 'none',
  PRIMARY KEY (`incidentlog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



DROP TABLE IF EXISTS `incident_status`;

CREATE TABLE `incident_status` (
  `incidentlog_id` varchar(255) NOT NULL,
  `incident_main` varchar(255) NOT NULL,
  `person_id` varchar(255) NOT NULL,
  PRIMARY KEY (`incidentlog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



DROP TABLE IF EXISTS `official_end_information`;

CREATE TABLE `official_end_information` (
  `official_id` varchar(255) NOT NULL,
  `first_name` varchar(69) NOT NULL DEFAULT 'none',
  `middle_name` varchar(69) NOT NULL DEFAULT 'none',
  `last_name` varchar(69) NOT NULL DEFAULT 'none',
  `suffix` varchar(69) NOT NULL DEFAULT 'none',
  `birth_date` varchar(69) NOT NULL DEFAULT 'none',
  `birth_place` varchar(69) NOT NULL DEFAULT 'none',
  `gender` varchar(69) NOT NULL DEFAULT 'none',
  `age` varchar(69) NOT NULL DEFAULT 'none',
  `civil_status` varchar(69) NOT NULL DEFAULT 'none',
  `religion` varchar(69) NOT NULL DEFAULT 'none',
  `nationality` varchar(69) NOT NULL DEFAULT 'none',
  `municipality` varchar(69) NOT NULL DEFAULT 'none',
  `zip` varchar(69) NOT NULL DEFAULT 'none',
  `barangay` varchar(69) NOT NULL DEFAULT 'none',
  `house_number` varchar(69) NOT NULL DEFAULT 'none',
  `street` varchar(69) NOT NULL DEFAULT 'none',
  `address` varchar(69) NOT NULL DEFAULT 'none',
  `email_address` varchar(69) NOT NULL DEFAULT 'none',
  `contact_number` varchar(69) NOT NULL DEFAULT 'none',
  `fathers_name` varchar(69) NOT NULL DEFAULT 'none',
  `mothers_name` varchar(69) NOT NULL DEFAULT 'none',
  `guardian` varchar(69) NOT NULL DEFAULT 'none',
  `guardian_contact` varchar(69) NOT NULL DEFAULT 'none',
  `image` varchar(255) NOT NULL DEFAULT 'none',
  `image_path` varchar(255) NOT NULL DEFAULT 'none',
  PRIMARY KEY (`official_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



DROP TABLE IF EXISTS `official_end_status`;

CREATE TABLE `official_end_status` (
  `official_id` varchar(255) NOT NULL,
  `position` varchar(69) NOT NULL DEFAULT 'none',
  `purok_id` varchar(255) NOT NULL,
  `senior` varchar(69) NOT NULL DEFAULT 'none',
  `term_from` varchar(69) NOT NULL DEFAULT 'none',
  `term_to` varchar(69) NOT NULL DEFAULT 'none',
  `pwd` varchar(69) NOT NULL DEFAULT 'none',
  `pwd_info` varchar(255) NOT NULL DEFAULT 'none',
  `single_parent` varchar(69) NOT NULL DEFAULT 'none',
  `status` varchar(69) NOT NULL DEFAULT 'none',
  `voters` varchar(69) NOT NULL DEFAULT 'none',
  `date_deleted` varchar(69) NOT NULL DEFAULT 'none',
  PRIMARY KEY (`official_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



DROP TABLE IF EXISTS `official_information`;

CREATE TABLE `official_information` (
  `a_i` int(11) NOT NULL AUTO_INCREMENT,
  `official_id` varchar(255) NOT NULL,
  `first_name` varchar(69) NOT NULL DEFAULT 'none',
  `middle_name` varchar(69) NOT NULL DEFAULT 'none',
  `last_name` varchar(69) NOT NULL DEFAULT 'none',
  `suffix` varchar(69) NOT NULL DEFAULT 'none',
  `birth_date` varchar(69) NOT NULL DEFAULT 'none',
  `birth_place` varchar(69) NOT NULL DEFAULT 'none',
  `gender` varchar(69) NOT NULL DEFAULT 'none',
  `age` varchar(69) NOT NULL DEFAULT 'none',
  `civil_status` varchar(69) NOT NULL DEFAULT 'none',
  `religion` varchar(69) NOT NULL DEFAULT 'none',
  `nationality` varchar(69) NOT NULL DEFAULT 'none',
  `province` varchar(69) NOT NULL DEFAULT 'none',
  `zip` varchar(69) NOT NULL DEFAULT 'none',
  `city` varchar(69) NOT NULL DEFAULT 'none',
  `house_number` varchar(69) NOT NULL DEFAULT 'none',
  `street` varchar(69) NOT NULL DEFAULT 'none',
  `address` varchar(69) NOT NULL DEFAULT 'none',
  `email_address` varchar(69) NOT NULL DEFAULT 'none',
  `contact_number` varchar(69) NOT NULL DEFAULT 'none',
  `fathers_name` varchar(69) NOT NULL DEFAULT 'none',
  `mothers_name` varchar(69) NOT NULL DEFAULT 'none',
  `guardian` varchar(69) NOT NULL DEFAULT 'none',
  `guardian_contact` varchar(69) NOT NULL DEFAULT 'none',
  `image` varchar(255) NOT NULL DEFAULT 'none',
  `image_path` varchar(255) NOT NULL DEFAULT 'none',
  UNIQUE KEY `a_i` (`a_i`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `official_information` VALUES (65,1029202518161844552,"Aris","","Aguila","","1973-01-01","Antipolo City","Male",52,"Single","","","","","","","","Teremil","",94609406970,"","","","","","");


DROP TABLE IF EXISTS `official_status`;

CREATE TABLE `official_status` (
  `a_i` int(11) NOT NULL AUTO_INCREMENT,
  `official_id` varchar(255) NOT NULL,
  `position` varchar(69) NOT NULL DEFAULT 'none',
  `purok_id` varchar(255) NOT NULL,
  `senior` varchar(69) NOT NULL DEFAULT 'none',
  `term_from` varchar(69) NOT NULL DEFAULT 'none',
  `term_to` varchar(69) NOT NULL DEFAULT 'none',
  `pwd` varchar(69) NOT NULL DEFAULT 'none',
  `pwd_info` varchar(255) NOT NULL DEFAULT 'none',
  `status` varchar(69) NOT NULL DEFAULT 'none',
  `voters` varchar(69) NOT NULL DEFAULT 'none',
  `single_parent` varchar(255) NOT NULL DEFAULT 'none',
  `date_added` varchar(69) NOT NULL DEFAULT 'none',
  `date_undeleted` varchar(255) NOT NULL DEFAULT 'none',
  UNIQUE KEY `a_i` (`a_i`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `official_status` VALUES (59,1029202518161844552,717828409372790110292025175818344,"","NO","2023-07-23","2025-07-23","NO","","ACTIVE","YES","NO","10/29/2025 06:16 PM","none");


DROP TABLE IF EXISTS `payments`;

CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reference_no` varchar(100) NOT NULL,
  `method` varchar(50) NOT NULL,
  `date_submitted` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `payments` VALUES (1,2147483647,"100.00",124134134,"GCash","2025-11-05 23:00:08"),
(2,2147483647,"100.00",124134134,"GCash","2025-11-05 23:00:31"),
(3,2147483647,"100.00",124134134,"GCash","2025-11-05 23:00:47");


DROP TABLE IF EXISTS `payments_record`;

CREATE TABLE `payments_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reference_no` varchar(100) NOT NULL,
  `method` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `date_submitted` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



DROP TABLE IF EXISTS `position`;

CREATE TABLE `position` (
  `a_i` int(11) NOT NULL AUTO_INCREMENT,
  `position_id` varchar(255) NOT NULL,
  `position` varchar(69) NOT NULL DEFAULT 'none',
  `position_limit` varchar(69) NOT NULL DEFAULT 'none',
  `position_description` varchar(255) NOT NULL DEFAULT 'none',
  `color` varchar(255) NOT NULL DEFAULT 'none',
  UNIQUE KEY `a_i` (`a_i`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `position` VALUES (22,619131249471207208162022141229307,"PRESIDENT",1,"","#4fb42e"),
(23,124707950268138210292025175638337,"VICE PRESIDENT",1,"","#29afaf"),
(25,144837147199673010292025175706513,"TREASURER",1,"","#181a95"),
(26,717828409372790110292025175818344,"DIRECTOR",1,"","#1e7335"),
(27,891444888698007310292025175838820,"AUDITOR",1,"","#ee747a"),
(28,131780449283631910292025175851351,"SECRETARY",1,"","#ec8fb6"),
(29,804967351385394411052025234739394,"joker",1,"","#0ef2a0");


DROP TABLE IF EXISTS `precint`;

CREATE TABLE `precint` (
  `a_i` int(11) NOT NULL AUTO_INCREMENT,
  `precint_id` varchar(255) NOT NULL,
  `precint` varchar(255) NOT NULL,
  PRIMARY KEY (`a_i`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `precint` VALUES (1,112430277815139107242022164651634,12313200),
(5,834679331034411909122022012433363,"Test 123");


DROP TABLE IF EXISTS `purok`;

CREATE TABLE `purok` (
  `a_i` int(11) NOT NULL AUTO_INCREMENT,
  `purok_id` varchar(255) NOT NULL,
  `purok` varchar(255) NOT NULL,
  `leader` varchar(255) NOT NULL,
  PRIMARY KEY (`a_i`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `purok` VALUES (2,916259339179300507242022155033612,"puirok","qweqwe"),
(5,74710938236700907272022172121040,"ewqe","wqewqeq");


DROP TABLE IF EXISTS `residence_information`;

CREATE TABLE `residence_information` (
  `a_i` int(11) NOT NULL AUTO_INCREMENT,
  `residence_id` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL DEFAULT 'none',
  `middle_name` varchar(255) NOT NULL DEFAULT 'none',
  `last_name` varchar(255) NOT NULL DEFAULT 'none',
  `age` varchar(11) NOT NULL,
  `suffix` varchar(255) NOT NULL DEFAULT 'none',
  `alias` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL DEFAULT 'none',
  `civil_status` varchar(36) NOT NULL DEFAULT 'none',
  `religion` varchar(36) NOT NULL DEFAULT 'none',
  `nationality` varchar(255) NOT NULL DEFAULT 'none',
  `contact_number` varchar(69) NOT NULL DEFAULT 'none',
  `email_address` varchar(255) NOT NULL DEFAULT 'none',
  `address` varchar(255) NOT NULL DEFAULT 'none',
  `birth_date` varchar(255) NOT NULL DEFAULT 'none',
  `birth_place` varchar(255) NOT NULL DEFAULT 'none',
  `province` varchar(69) NOT NULL DEFAULT 'none',
  `zip` varchar(69) NOT NULL DEFAULT 'none',
  `city` varchar(69) NOT NULL DEFAULT 'none',
  `house_number` varchar(69) NOT NULL DEFAULT 'none',
  `street` varchar(69) NOT NULL DEFAULT 'none',
  `fathers_name` varchar(255) NOT NULL DEFAULT 'none',
  `mothers_name` varchar(255) NOT NULL DEFAULT 'none',
  `guardian` varchar(69) NOT NULL DEFAULT 'none',
  `guardian_contact` varchar(69) NOT NULL DEFAULT 'none',
  `occupation` varchar(255) NOT NULL,
  `employer_name` varchar(255) NOT NULL,
  `family_relation` varchar(255) NOT NULL,
  `national_number` varchar(255) NOT NULL,
  `sss_number` varchar(255) NOT NULL,
  `tin_number` varchar(255) NOT NULL,
  `gsis_number` varchar(255) NOT NULL,
  `pagibig_number` varchar(255) NOT NULL,
  `philhealth_number` varchar(255) NOT NULL,
  `bloodtype` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT 'none',
  `image_path` varchar(255) NOT NULL DEFAULT 'none',
  UNIQUE KEY `a_` (`a_i`)
) ENGINE=InnoDB AUTO_INCREMENT=184 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `residence_information` VALUES (182,96230707934287,"Brian Paul Gabriel","Pacis","Bulahan",26,"","","Male","Single","Catholic","Filipino",09776805531,"","Teremil","1998-12-20","Quezon City","","","","","","","","","","","","","","","","","","","","",""),
(183,62636923746663,"Maria Rubelina","Basco","Paguio",23,"","","Female","Single","Catholic","Filipino",09279294430,"","Teremil","2002-09-02","Dinalupihan, Bataan","","","","","","","","","","","","","","","","","","","","","");


DROP TABLE IF EXISTS `residence_status`;

CREATE TABLE `residence_status` (
  `a_i` int(11) NOT NULL AUTO_INCREMENT,
  `residence_id` varchar(255) NOT NULL,
  `status` varchar(69) NOT NULL DEFAULT 'none',
  `is_approved` varchar(255) NOT NULL,
  `voters` varchar(69) NOT NULL DEFAULT 'none',
  `pwd` varchar(69) NOT NULL DEFAULT 'none',
  `pwd_info` varchar(255) NOT NULL DEFAULT 'none',
  `senior` varchar(69) NOT NULL DEFAULT 'none',
  `single_parent` varchar(69) NOT NULL DEFAULT 'none',
  `wra` varchar(255) NOT NULL,
  `4ps` varchar(255) NOT NULL,
  `purok_id` varchar(255) NOT NULL,
  `precint_id` varchar(255) NOT NULL,
  `archive` varchar(69) NOT NULL DEFAULT 'none',
  `date_added` varchar(69) NOT NULL DEFAULT 'none',
  `date_archive` varchar(69) NOT NULL DEFAULT 'none',
  `date_unarchive` varchar(69) NOT NULL DEFAULT 'none',
  UNIQUE KEY `a_i` (`a_i`)
) ENGINE=InnoDB AUTO_INCREMENT=184 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `residence_status` VALUES (182,96230707934287,"ACTIVE","","YES","NO","","NO","NO","","","","","NO","10/29/2025 06:10 PM","none","none"),
(183,62636923746663,"ACTIVE","","YES","NO","","NO","NO","","","","","NO","10/29/2025 06:12 PM","11/05/2025 04:12 PM","11/05/2025 04:13 PM");


DROP TABLE IF EXISTS `taa_information`;

CREATE TABLE `taa_information` (
  `id` varchar(255) NOT NULL,
  `address` varchar(69) NOT NULL DEFAULT 'none',
  `postal_address` varchar(255) NOT NULL DEFAULT 'none',
  `image` varchar(255) NOT NULL DEFAULT 'none',
  `image_path` varchar(255) NOT NULL DEFAULT 'none',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `taa_information` VALUES (32432432432432432,"Antipolo","Postal Address","Lowgo.png","../assets/dist/img/Lowgo.png");


DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `a_i` int(11) NOT NULL AUTO_INCREMENT,
  `id` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL DEFAULT 'none',
  `middle_name` varchar(255) NOT NULL DEFAULT 'none',
  `last_name` varchar(255) NOT NULL DEFAULT 'none',
  `username` varchar(255) NOT NULL DEFAULT 'none',
  `password` varchar(255) NOT NULL DEFAULT 'none',
  `user_type` varchar(255) NOT NULL DEFAULT 'none',
  `contact_number` varchar(255) NOT NULL DEFAULT 'none',
  `image` varchar(255) NOT NULL DEFAULT 'none',
  `image_path` varchar(255) NOT NULL DEFAULT 'none',
  UNIQUE KEY `a_i` (`a_i`)
) ENGINE=InnoDB AUTO_INCREMENT=207 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` VALUES (52,1506135735699,"Admin","Admin","Admin","admin123","admin123","admin",11111111111,"182708071361a0f053c94fb.png","../assets/dist/img/182708071361a0f053c94fb.png"),
(195,174668789044820710152022021619941,"Secretary","Secretary","Secretary","secretary123","secretary123","secretary",99999999999,"",""),
(205,96230707934287,"Brian Paul Gabriel","Pacis","Bulahan",96230707934287,10292025181011608,"resident",09776805531,"",""),
(206,62636923746663,"Maria Rubelina","Basco","Paguio",62636923746663,"123g65","resident",09279294430,"","");


DROP TABLE IF EXISTS `vaccine`;

CREATE TABLE `vaccine` (
  `a_i` int(11) NOT NULL AUTO_INCREMENT,
  `vaccine_id` varchar(255) NOT NULL,
  `residence_id` varchar(255) NOT NULL,
  `vaccine` varchar(255) NOT NULL,
  `second_vaccine` varchar(255) NOT NULL,
  `first_dose_date` varchar(255) NOT NULL,
  `second_dose_date` varchar(255) NOT NULL,
  `booster` varchar(255) NOT NULL,
  `booster_date` varchar(255) NOT NULL,
  `second_booster` varchar(255) NOT NULL,
  `second_booster_date` varchar(255) NOT NULL,
  PRIMARY KEY (`a_i`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `vaccine` VALUES (35,3267818051106726,16455182440138,"first","second","2022-10-01","2022-10-02","first b","2022-10-03","second b","2022-10-04"),
(36,9517807083807772,54278971251733,"first","second","2022-10-01","2022-10-02","first b","2022-10-03","second b","2022-10-04");


DROP TABLE IF EXISTS `wra`;

CREATE TABLE `wra` (
  `a_i` int(11) NOT NULL AUTO_INCREMENT,
  `resident_id` varchar(255) NOT NULL,
  `nhts` varchar(255) NOT NULL,
  `pregnant` varchar(255) NOT NULL,
  `menopause` varchar(255) NOT NULL,
  `achieving` varchar(255) NOT NULL,
  `ofw` varchar(255) NOT NULL,
  `fp_method` varchar(255) NOT NULL,
  `desire_limit` varchar(255) NOT NULL,
  `desire_space` varchar(255) NOT NULL,
  `remarks` varchar(255) NOT NULL,
  PRIMARY KEY (`a_i`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `wra` VALUES (62,16455182440138,"NTHS","YES","YES","YES","YES","FP Method","YES","YES",""),
(63,54278971251733,"NTHS","YES","YES","YES","YES","FP Method","YES","YES","");


SET foreign_key_checks = 1;
