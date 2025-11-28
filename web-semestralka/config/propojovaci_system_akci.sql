
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `propojovaci_system_akci`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

--
-- Vypisuji data pro tabulku `category`
--

INSERT INTO `category` (`id`, `name`) VALUES
(1, 'Koncert'),
(2, 'Workshop'),
(3, 'Výstava'),
(4, 'Přednáška'),
(5, 'Festival'),
(6, 'Jiné');

-- --------------------------------------------------------

--
-- Struktura tabulky `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `capacity` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `date` datetime NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `photoPath` varchar(255) DEFAULT NULL,
  `place_id` int(11) DEFAULT NULL,
  `approved` enum('čeká se na schválení','schváleno','zamítnuto') DEFAULT 'čeká se na schválení',
  `message` text DEFAULT NULL,
  `created` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

--
-- Vypisuji data pro tabulku `events`
--

INSERT INTO `events` (`id`, `user_id`, `name`, `capacity`, `description`, `date`, `category_id`, `photoPath`, `place_id`, `approved`, `message`, `created`) VALUES
(1, 5, 'Konzert kapely Kabát na Náměstí Republiky', 100, '<p>Jedeme za Vámi. Léto pomalu začíná a mi jsme se rozhodli přijet se podívat do Plzně.<br>Na Náměstí Republiky Vám zahrajeme naše nejlepší hity jako:</p><ul><li>Moderní děvče</li><li>Colorado</li><li>Malá dáma</li><li>a mnoho dalšího</li></ul><p>Vstupné je zdarma.</p><p>Doražte a užije si atmosféu s námi.&nbsp;</p><p>Ahoj!&nbsp;</p>', '2026-05-17 19:00:00', 1, '/www/img/events/event_5_1763748409.jpg', 11, 'schváleno', NULL, '2025-11-21 19:06:49'),
(2, 5, 'Kabáti v Depu', 150, '<p>Jedeme za Vámi. Léto pomalu začíná a mi jsme se rozhodli přijet se podívat do Plzně.<br>V Depu 2015 Vám zahrajeme naše nejlepší hity jako:</p><ul><li>Moderní děvče</li><li>Colorado</li><li>Malá dáma</li><li>a mnoho dalšího</li></ul><p>Vstupné je zdarma.</p><p>Doražte a užije si atmosféu s námi.&nbsp;</p><p>Ahoj!&nbsp;</p>', '2026-07-16 20:00:00', 1, '/www/img/events/event_5_1763748501.jpg', 9, 'schváleno', NULL, '2025-11-21 19:08:21'),
(3, 3, 'Jak si postavit barák snů', 40, '<p>Workshop <strong>„Jak si postavit barák snů“</strong> je pro všechny, kdo plánují vlastní dům a nechtějí se spálit na zbytečných chybách.<br>Ukážu vám celý proces od výběru pozemku až po nastěhování – včetně reálných čísel z praxe.<br>Dozvíte se, jak hlídat rozpočet, smlouvy, stavební firmu i komunikaci s úřady.<br>Probereme hlavní stavební technologie, jejich výhody/nevýhody a typické problémy, na které si dát pozor.<br>Na konci budete mít jasný přehled kroků, které vás čekají, a konkrétní tipy, jak ušetřit peníze, čas i nervy.</p><p><strong>Co si odnesete v bodech:</strong></p><ul><li>přehled kroků: pozemek → projekt → povolení → stavba → kolaudace</li><li>tipy, kde se nejčastěji schovávají skryté náklady</li><li>jak vybírat firmu / dodavatele a co mít ve smlouvách</li><li>srovnání zděný dům vs. dřevostavba (plusy/mínusy)</li><li>prostor pro dotazy k vašemu konkrétnímu záměru domu</li></ul><p>Cena workshopu: 250 Kč</p>', '2025-08-12 18:00:00', 2, '/www/img/events/event_3_1763748787.jpg', 7, 'schváleno', NULL, '2025-11-21 19:13:07'),
(4, 3, 'Jak si postavit barák snů', 40, '<p>Workshop <strong>„Jak si postavit barák snů“</strong> je pro všechny, kdo plánují vlastní dům a nechtějí se spálit na zbytečných chybách.<br>Ukážu vám celý proces od výběru pozemku až po nastěhování – včetně reálných čísel z praxe.<br>Dozvíte se, jak hlídat rozpočet, smlouvy, stavební firmu i komunikaci s úřady.<br>Probereme hlavní stavební technologie, jejich výhody/nevýhody a typické problémy, na které si dát pozor.<br>Na konci budete mít jasný přehled kroků, které vás čekají, a konkrétní tipy, jak ušetřit peníze, čas i nervy.</p><p><strong>Co si odnesete v bodech:</strong></p><ul><li>přehled kroků: pozemek → projekt → povolení → stavba → kolaudace</li><li>tipy, kde se nejčastěji schovávají skryté náklady</li><li>jak vybírat firmu / dodavatele a co mít ve smlouvách</li><li>srovnání zděný dům vs. dřevostavba (plusy/mínusy)</li><li>prostor pro dotazy k vašemu konkrétnímu záměru domu</li></ul><p>Cena workshopu: 250 Kč</p>', '2026-05-20 11:11:00', 2, '/www/img/events/event_3_1763748828.jpg', 7, 'schváleno', NULL, '2025-11-21 19:13:48'),
(5, 3, 'Jak si postavit barák snů', 50, '<p>Workshop <strong>„Jak si postavit barák snů“</strong> je pro všechny, kdo plánují vlastní dům a nechtějí se spálit na zbytečných chybách.<br>Ukážu vám celý proces od výběru pozemku až po nastěhování – včetně reálných čísel z praxe.<br>Dozvíte se, jak hlídat rozpočet, smlouvy, stavební firmu i komunikaci s úřady.<br>Probereme hlavní stavební technologie, jejich výhody/nevýhody a typické problémy, na které si dát pozor.<br>Na konci budete mít jasný přehled kroků, které vás čekají, a konkrétní tipy, jak ušetřit peníze, čas i nervy.</p><p><strong>Co si odnesete v bodech:</strong></p><ul><li>přehled kroků: pozemek → projekt → povolení → stavba → kolaudace</li><li>tipy, kde se nejčastěji schovávají skryté náklady</li><li>jak vybírat firmu / dodavatele a co mít ve smlouvách</li><li>srovnání zděný dům vs. dřevostavba (plusy/mínusy)</li><li>prostor pro dotazy k vašemu konkrétnímu záměru domu</li></ul><p>Cena workshopu: 250 Kč</p>', '2026-07-14 18:00:00', 2, '/www/img/events/event_3_1763748856.jpg', 7, 'zamítnuto', 'Dostali jsme stížnost, že TechTower nebyl s Vámi moc spokejený. Prý vaše publikum bylo hlučné a neslušné k personálu. Pokud chcete akci pořádat, nejprve se domluvte s TechTowerem.\nTomáš Svoboda', '2025-11-21 19:14:16'),
(6, 3, 'Jak na vlastní svatbu?', 60, '<p>Workshop <strong>„Jak na vlastní svatbu?“</strong> je pro páry, které si chtějí svatbu zorganizovat samy a mít přehled nad rozpočtem, dodavateli i harmonogramem dne.<br>Projdeme spolu celý proces od prvního nápadu a rozpočtu až po posledního hosta, který odchází domů.<br>Ukážu vám, jak sestavit realistický rozpočet, kde se nejčastěji připlácí zbytečně a na čem se naopak nevyplatí šetřit.<br>Dozvíte se, jak vybírat místo, catering, fotografa, DJ/kapelu a co mít vždy písemně ve smlouvách.<br>Odnesete si jasný plán kroků, chytré checklisty a konkrétní tipy, jak zvládnout svatbu bez chaosu a zbytečného stresu.</p><p><strong>Co si odnesete v bodech:</strong></p><ul><li>přehled kroků: termín → rozpočet → místo → dodavatelé → harmonogram dne</li><li>vzorový svatební harmonogram a kontrolní checklist</li><li>tipy na komunikaci s dodavateli a co hlídat ve smlouvách</li><li>doporučení, kde obvykle utíkají peníze a jak tomu předejít</li><li>rady, jak zapojit svědky/rodinu, aby vám opravdu pomohli a neudělali z toho zmatek</li></ul>', '2025-06-12 18:00:00', 4, '/www/img/events/event_3_1763748965.webp', 7, 'schváleno', NULL, '2025-11-21 19:16:05'),
(7, 3, 'Jak na vlastní svatbu?', 50, '<p>Workshop <strong>„Jak na vlastní svatbu?“</strong> je pro páry, které si chtějí svatbu zorganizovat samy a mít přehled nad rozpočtem, dodavateli i harmonogramem dne.<br>Projdeme spolu celý proces od prvního nápadu a rozpočtu až po posledního hosta, který odchází domů.<br>Ukážu vám, jak sestavit realistický rozpočet, kde se nejčastěji připlácí zbytečně a na čem se naopak nevyplatí šetřit.<br>Dozvíte se, jak vybírat místo, catering, fotografa, DJ/kapelu a co mít vždy písemně ve smlouvách.<br>Odnesete si jasný plán kroků, chytré checklisty a konkrétní tipy, jak zvládnout svatbu bez chaosu a zbytečného stresu.</p><p><strong>Co si odnesete v bodech:</strong></p><ul><li>přehled kroků: termín → rozpočet → místo → dodavatelé → harmonogram dne</li><li>vzorový svatební harmonogram a kontrolní checklist</li><li>tipy na komunikaci s dodavateli a co hlídat ve smlouvách</li><li>doporučení, kde obvykle utíkají peníze a jak tomu předejít</li><li>rady, jak zapojit svědky/rodinu, aby vám opravdu pomohli a neudělali z toho zmatek</li></ul>', '2026-07-12 12:00:00', 4, '/www/img/events/event_3_1763748998.webp', 7, 'schváleno', NULL, '2025-11-21 19:16:38'),
(8, 3, 'Základy surfování', 25, '<p>Workshop <strong>„Základy surfování“</strong> je pro všechny, kdo chtějí bezpečně a srozumitelně proniknout do světa surfování od úplného začátku.<br>Vysvětlíme si základní techniku na suchu, správný postoj na prkně, pádlování a první „take-off“ na vlně.<br>Dozvíte se, jak si vybrat vhodné prkno a neopren podle vaší výšky, váhy a podmínek na spotu.<br>Projdeme bezpečnost na vodě, priority na vlnách a základní „pravidla slušného chování“ v line-upu.<br>Cílem je, abyste po workshopu věděli, co dělat v moři, nebáli se vln a uměli pokračovat v tréninku i sami.</p><p><strong>Co si odnesete v bodech:</strong></p><ul><li>základní techniku pádlování, vstávání na prkno a správný postoj</li><li>přehled typů surfů a jak si vybrat ten správný pro začátek</li><li>pravidla bezpečnosti a priority na vlnách (surf etiketa)</li><li>tipy, jak se připravit fyzicky i mentálně před vstupem do vody</li><li>jednoduchý plán, jak pokračovat v tréninku po workshopu (cvičení na souši + ve vodě)</li></ul>', '2026-04-04 16:00:00', 2, '/www/img/events/event_3_1763749084.webp', 7, 'schváleno', NULL, '2025-11-21 19:18:04'),
(9, 3, 'Výstava moderního umění', 80, '<p>Výstava <strong>„Výstava moderního umění“</strong> představuje současné autory, kteří pracují s aktuálními tématy, novými technologiemi a netradičními materiály.<br>Návštěvníci uvidí široké spektrum přístupů – od abstraktních pláten a instalací až po digitální umění a interaktivní objekty.<br>Důraz klademe na emoce, osobní výpověď autorů a otázky, které jejich díla otevírají – od každodenní reality až po společenská témata.<br>Součástí výstavy jsou také krátké texty a doprovodné materiály, které pomohou lépe pochopit kontext a záměr jednotlivých děl.<br>Výstava je vhodná pro širokou veřejnost – od úplných laiků až po návštěvníky, kteří se v moderním umění už orientují.</p><p><strong>Co na výstavě najdete:</strong></p><ul><li>malby, sochy, instalace a digitální díla od více autorů</li><li>interaktivní prvky, do kterých se může návštěvník zapojit</li><li>stručné komentáře k jednotlivým dílům a autorům</li><li>možnost projít si výstavu volně, nebo s využitím připravených tematických okruhů</li><li>klidný prostor pro zamyšlení, diskusi a vlastní interpretaci děl</li></ul>', '2026-08-01 17:00:00', 3, '/www/img/events/event_3_1763749518.png', 9, 'schváleno', NULL, '2025-11-21 19:25:18'),
(10, 3, 'Moje cesty po Ibize', 50, '<p>Na přednášce <strong>„Moje cesty po Ibize“</strong> se podíváme na Ibizu jinak, než jak ji ukazují cestovky a party videa.<br>Budu mluvit o místech, kde se dá zažít klid, příroda a lokální atmosféra bez tlačenic turistů.<br>Ukážu konkrétní tipy na pláže, výlety, západy slunce i podniky, kde má smysl utratit peníze.<br>Dozvíte se, kolik co ve skutečnosti stojí, jak se po ostrově rozumně pohybovat a čemu se raději vyhnout.</p><p><strong>O čem budu mluvit konkrétně:</strong></p><ul><li>jak plánovat cestu na Ibizu (letenky, ubytování, auto / skútr, termín)</li><li>rozdíly mezi „party Ibizou“ a klidnějšími částmi ostrova</li><li>moje oblíbené pláže, vyhlídky a místa na západ slunce</li><li>kde se dobře najíst a napít, aniž by to zruinovalo rozpočet</li><li>jak si užít atmosféru ostrova i bez denního paření</li></ul><p><strong>Pro koho je přednáška:</strong></p><ul><li>pro ty, kteří chtějí na Ibizu poprvé a neví, kde začít</li><li>pro lidi, co chtějí kombinaci moře, výletů a trochu nightlife</li><li>pro každého, kdo dává přednost reálným zkušenostem před katalogem cestovky</li></ul>', '2026-07-30 18:00:00', 4, '/www/img/events/event_3_1763749733.jpg', 10, 'schváleno', NULL, '2025-11-21 19:28:53'),
(11, 3, 'Moje cesty po Indonésii', 50, '<p>Přednáška <strong>„Moje cesty po Indonésii“</strong> ukazuje Indonésii tak, jak ji poznáte, když tam strávíte delší čas – nejen Bali, ale i další ostrovy, kde turisté často vůbec nejsou.<br>Mluvím o tom, jak se cestuje mezi ostrovy, jaké jsou rozdíly mezi oblastmi, co mě překvapilo v každodenním životě místních a kde dává smysl strávit víc dní.<br>Součástí jsou i konkrétní příklady tras, reálné ceny a situace, které se na cestě opravdu staly – včetně toho, co bych dnes udělal jinak.</p><h4><strong>Témata, která otevřeme:</strong></h4><ul><li>rozdíl mezi „instagramovým“ Bali a klidnějšími částmi Indonésie</li><li>přesuny mezi ostrovy (lodě, vnitrostátní lety, skútr, vlaky)</li><li>příroda vs. města: sopky, rýžová pole, džungle, chrámy, chaotická města</li><li>jídlo, kultura, náboženství a základní „nepsaná pravidla“ chování</li></ul><h4><strong>Co si z přednášky odnesete:</strong></h4><ul><li>konkrétní tipy na místa, která stojí za to (a kterým se spíš vyhnout)</li><li>orientační rozpočty podle stylu cestování (lowcost / střední rozpočet)</li><li>jednoduché návrhy itinerářů na 2–3 týdny</li><li>praktické rady k bezpečnosti, SIM kartám, platebním kartám a vyřizování víz</li></ul><p>Cena: 100 Kč</p>', '2026-04-23 17:30:00', 4, '/www/img/events/event_3_1763749880.jpg', 7, 'schváleno', NULL, '2025-11-21 19:31:20'),
(12, 12, 'Food Festival', 150, '<p>Food Festival je jednodenní/víkendová akce pro všechny, kdo rádi ochutnávají nové chutě a street food z různých koutů světa.<br>Na jednom místě se potkají food trucky, lokální restaurace i malé značky s vlastními produkty.<br>Součástí programu budou i ukázky vaření, krátké workshopy a doprovodný program pro děti i dospělé.<br>Cílem festivalu je dát lidem možnost ochutnat, co běžně v menu nenajdou, a podpořit lokální gastro scénu.</p><p><strong>Co na festivalu najdete:</strong></p><ul><li>stánky s jídlem z různých kuchyní (burgery, asijská kuchyně, vegetarián/vegan, sladké)</li><li>lokální pivovary, vinaře a nealko limonády / kávu</li><li>ukázky vaření a krátké cooking show</li><li>chill zónu k posezení, zázemí pro rodiny s dětmi</li><li>doprovodný program (hudba, soutěže, hlasování o „nejlepší stánek“)</li></ul>', '2026-05-24 17:00:00', 5, '/www/img/events/event_12_1763750427.png', 9, 'schváleno', NULL, '2025-11-21 19:40:27'),
(13, 12, 'Festival vaření', 50, '<p>Během dne poběží různé kuchařské workshopy, ukázky technik a praktické lekce pro začátečníky i pokročilejší.<br>Zaměříme se na jednoduchá, ale funkční jídla do běžného života i na pár „wow“ receptů pro speciální příležitosti.<br>Součástí bude i prostor na dotazy, sdílení tipů a ochutnávky toho, co se na místě uvaří.</p><p><strong>Co na festivalu najdete:</strong></p><ul><li>tematické workshopy (základy vaření, rychlá večeře, dezerty, grilování apod.)</li><li>živé cooking show s vysvětlováním postupů krok za krokem</li><li>možnost si některé recepty osobně vyzkoušet</li><li>receptové kartičky / PDF ke stažení na doma</li><li>zónu pro dotazy a konzultace s kuchaři</li></ul>', '2026-08-29 16:00:00', 5, '/www/img/events/event_12_1763751008.png', 7, 'schváleno', NULL, '2025-11-21 19:50:08'),
(14, 13, 'Taneční začátečníci', 30, '<p>Workshop <strong>„Taneční začátečníci“</strong> je pro všechny, kteří chtějí začít tancovat od úplných základů, bez stresu a ostychu.<br>Projdeme si držení těla, základní kroky a jednoduché otočky tak, aby se na parketu cítil dobře i ten, kdo „má dvě levé“.<br>Vysvětlíme si, jak poslouchat hudbu, chytit rytmus a jak vést / nechat se vést v páru.<br>Tempo přizpůsobíme skupině, vše budeme opakovat a dostanete prostor se ptát.</p><p><strong>Pro koho je workshop:</strong></p><ul><li>úplní začátečníci, kteří nikdy nebyli v tanečních</li><li>ti, kdo si chtějí oprášit základy před svatbou, plesem nebo večírkem</li><li>páry i jednotlivci (partner/ka není nutná)</li></ul><p><strong>Co se naučíte:</strong></p><ul><li>základní kroky vybraných tanců (např. waltz, polka / jednoduchý latin)</li><li>práci s tělem a držením, aby tanec vypadal přirozeně</li><li>jednoduchou komunikaci v páru (vedení / následování)</li><li>jak si tanec užít bez zbytečného stresu a porovnávání s ostatními</li></ul><p>Cena: 3 000 Kč</p>', '2026-02-12 17:00:00', 2, '/www/img/events/event_13_1763751412.jpeg', 10, 'schváleno', NULL, '2025-11-21 19:56:52'),
(15, 13, 'Taneční začátečníci', 30, '<p>Workshop <strong>„Taneční začátečníci“</strong> je pro všechny, kteří chtějí začít tancovat od úplných základů, bez stresu a ostychu.<br>Projdeme si držení těla, základní kroky a jednoduché otočky tak, aby se na parketu cítil dobře i ten, kdo „má dvě levé“.<br>Vysvětlíme si, jak poslouchat hudbu, chytit rytmus a jak vést / nechat se vést v páru.<br>Tempo přizpůsobíme skupině, vše budeme opakovat a dostanete prostor se ptát.</p><p><strong>Pro koho je workshop:</strong></p><ul><li>úplní začátečníci, kteří nikdy nebyli v tanečních</li><li>ti, kdo si chtějí oprášit základy před svatbou, plesem nebo večírkem</li><li>páry i jednotlivci (partner/ka není nutná)</li></ul><p><strong>Co se naučíte:</strong></p><ul><li>základní kroky vybraných tanců (např. waltz, polka / jednoduchý latin)</li><li>práci s tělem a držením, aby tanec vypadal přirozeně</li><li>jednoduchou komunikaci v páru (vedení / následování)</li><li>jak si tanec užít bez zbytečného stresu a porovnávání s ostatními</li></ul><p>Cena: 3 000 Kč</p>', '2026-05-14 17:00:00', 2, '/www/img/events/event_13_1763751448.jpeg', 10, 'schváleno', NULL, '2025-11-21 19:57:28'),
(16, 13, 'Taneční pokročilý', 30, '<p>Workshop <strong>„Taneční pokročilý“</strong> je pro ty, kteří zvládají základní kroky a chtějí vypadat na parketu jistěji, technicky líp a víc „profesionálně“.<br>Zaměříme se na techniku, těžiště, vedení v páru, přesný rytmus a plynulé přechody mezi figurami.<br>Budeme rozšiřovat variace známých tanců a přidáme styling rukou, práce s trupem a výraz.<br>Součástí je i zpětná vazba k vašemu tanci a tipy, jak trénovat efektivně doma.</p><p><strong>Pro koho je workshop:</strong></p><ul><li>tanečníci po základních / prodloužených kurzech</li><li>páry, které chtějí jistější výkon na plesech, svatbách a soutěžích neoficiální úrovně</li><li>všichni, kdo už zvládají základy a chtějí technicky i vizuálně „level výš“</li></ul><p><strong>Co se naučíte:</strong></p><ul><li>náročnější variace standardních a latinsko-amerických tanců (dle složení skupiny)</li><li>lepší vedení / reagování v páru, práce s rámem a těžištěm</li><li>muzikalitu: jak tanec napojit na hudbu (akcenty, pauzy, dynamika)</li><li>styling rukou, hlavy a těla tak, aby tanec vypadal čistěji a sebevědoměji</li></ul>', '2026-03-13 17:00:00', 2, '/www/img/events/event_13_1763751557.jpg', 10, 'schváleno', NULL, '2025-11-21 19:59:17'),
(17, 13, 'Taneční senioři', 30, '<p>Workshop <strong>„Taneční senioři“</strong> je určený pro ty, kteří si chtějí užít tanec v klidnějším tempu, rozhýbat tělo a přitom se potkat s dalšími lidmi.<br>Kroky budeme vysvětlovat pomalu, srozumitelně a s ohledem na komfort a případná zdravotní omezení.<br>Zaměříme se na jednoduché společenské tance a základní pohyb v páru tak, aby se každý cítil jistě a bezpečně.<br>Důraz klademe na radost z pohybu, příjemnou atmosféru a společnost, ne na výkon nebo dokonalou techniku.</p><p><strong>Pro koho je workshop:</strong></p><ul><li>senioři, kteří chtějí začít s tancem nebo se k němu po letech vrátit</li><li>páry i jednotlivci (partner/ka se dá domluvit na místě podle možností skupiny)</li><li>všichni, kdo chtějí spojit lehký pohyb, hudbu a společnost ostatních</li></ul><p><strong>Co se naučíte:</strong></p><ul><li>jednoduché kroky základních tanců (valčík, polka, pomalejší latina apod.)</li><li>jak se bezpečně pohybovat na parketu a pracovat s držením těla</li><li>základní vedení v páru a spolupráci s partnerem/partnerkou</li><li>jak si tanec užít bez stresu, v tempu, které je vám příjemné</li></ul><p>Cena: 1 500 Kč</p>', '2026-07-19 16:00:00', 2, '/www/img/events/event_13_1763751686.jpg', 10, 'schváleno', NULL, '2025-11-21 20:01:26'),
(18, 6, 'Tanec na ulici', 25, '<p>Workshop <strong>„Tanec na ulici“</strong> je zaměřený na streetové styly a tanec v běžném městském prostoru (náměstí, parky, ulice).<br>Budeme pracovat s rytmem, energií a tím, jak využít okolí – lavičky, stěny, schody – jako součást choreografie.<br>Součástí je i práce se sebevědomím: jak tančit před lidmi, nenechat se rozhodit pohledy okolí a udržet si vlastní flow.<br>Na závěr zkusíme krátkou společnou „performance“ v reálném prostoru (dle možností místa a skupiny).</p><p><strong>Pro koho je workshop:</strong></p><ul><li>pro začátečníky i mírně pokročilé, které láká street dance / freestyle</li><li>pro ty, kdo chtějí získat odvahu tancovat i mimo sál</li><li>pro jednotlivce i skupinky kamarádů</li></ul><p><strong>Co se naučíte:</strong></p><ul><li>základní prvky street dance (groovy kroky, práce s tělem, rytmus)</li><li>jak využít okolní prostředí v tanci (rekvizity, prostor, směry)</li><li>jak zvládnout trému a tancovat před lidmi s větší jistotou</li><li>krátkou streetovou choreografii nebo freestyle strukturu, kterou si odnesete domů</li></ul>', '2026-06-14 17:00:00', 2, '/www/img/events/event_6_1763753921.jpg', 11, 'schváleno', NULL, '2025-11-21 20:38:41'),
(19, 6, 'Tanec na ulici', 25, '<p>Workshop <strong>„Tanec na ulici“</strong> je zaměřený na streetové styly a tanec v běžném městském prostoru (náměstí, parky, ulice).<br>Budeme pracovat s rytmem, energií a tím, jak využít okolí – lavičky, stěny, schody – jako součást choreografie.<br>Součástí je i práce se sebevědomím: jak tančit před lidmi, nenechat se rozhodit pohledy okolí a udržet si vlastní flow.<br>Na závěr zkusíme krátkou společnou „performance“ v reálném prostoru (dle možností místa a skupiny).</p><p><strong>Pro koho je workshop:</strong></p><ul><li>pro začátečníky i mírně pokročilé, které láká street dance / freestyle</li><li>pro ty, kdo chtějí získat odvahu tancovat i mimo sál</li><li>pro jednotlivce i skupinky kamarádů</li></ul><p><strong>Co se naučíte:</strong></p><ul><li>základní prvky street dance (groovy kroky, práce s tělem, rytmus)</li><li>jak využít okolní prostředí v tanci (rekvizity, prostor, směry)</li><li>jak zvládnout trému a tancovat před lidmi s větší jistotou</li><li>krátkou streetovou choreografii nebo freestyle strukturu, kterou si odnesete domů</li></ul>', '2026-09-19 18:00:00', 2, '/www/img/events/event_6_1763753951.jpg', 11, 'schváleno', NULL, '2025-11-21 20:39:11'),
(20, 6, 'Dracula', 240, '<p>Přijďte se podívat na prkna co znamenají svět na premiéru muzikálu Dracula</p>', '2026-02-12 19:00:00', 6, '/www/img/events/event_6_1763754048.jpg', 8, 'schváleno', NULL, '2025-11-21 20:40:48'),
(21, 6, 'Dracula', 240, '<p>Přijďte se podívat na prkna co znamenají svět na premiéru muzikálu Dracula</p>', '2026-05-14 19:00:00', 6, '/www/img/events/event_6_1763754085.jpg', 8, 'schváleno', NULL, '2025-11-21 20:41:25'),
(22, 2, 'Jak na shortování akcií', 60, '<p><strong>Jak na shortování akcií</strong> – intenzivní workshop pro lidi, kteří chtějí pochopit, jak funguje sázení na pokles, <i>ještě než u toho spálí účet</i>.</p><p>Co to shortování vlastně je vysvětlíme na úplně konkrétním příkladu: půjčení akcií, jejich prodej, nákup zpět, marže, poplatky, co se děje při prudkém růstu ceny.<br>Ukážeme rozdíl mezi klasickým shortem přes margin účet, využitím CFD/derivátů a proč je každá varianta jinak riziková.<br>Podíváme se na situace, kdy short dává ekonomicky smysl (hedging, přeceněné firmy, bubliny) a kdy je to jen čisté gamblerství.<br>Rozpitváme pojmy jako <i>short squeeze</i>, margin call, likvidace pozice a ukážeme, jak rychle může ztráta přerůst původní vklad.<br>Projdeme si také základní risk management: velikost pozice, stop-loss, práce s pákou, diverzifikace a scénáře „co když to půjde proti mně o 10 / 20 / 50 %“.<br>Součástí budou modelové příklady na reálných grafech – bez doporučování konkrétních titulů, čistě vzdělávací pohled na mechaniku a riziko.</p><p>Na závěr jasně zdůrazníme, že shortování je pokročilá a vysoce riziková strategie, nevhodná pro začátečníky, a workshop neslouží jako osobní investiční doporučení, ale jako návod, <strong>jak chápat, co vlastně děláte, pokud se do shortů pouštíte</strong>.</p>', '2026-03-14 18:00:00', 2, '/www/img/events/event_2_1763754217.jpg', 7, 'schváleno', NULL, '2025-11-21 20:43:37'),
(23, 2, 'Jak na shortování akcií', 140, '<p><strong>Jak na shortování akcií</strong> – intenzivní workshop pro lidi, kteří chtějí pochopit, jak funguje sázení na pokles, <i>ještě než u toho spálí účet</i>.</p><p>Co to shortování vlastně je vysvětlíme na úplně konkrétním příkladu: půjčení akcií, jejich prodej, nákup zpět, marže, poplatky, co se děje při prudkém růstu ceny.<br>Ukážeme rozdíl mezi klasickým shortem přes margin účet, využitím CFD/derivátů a proč je každá varianta jinak riziková.<br>Podíváme se na situace, kdy short dává ekonomicky smysl (hedging, přeceněné firmy, bubliny) a kdy je to jen čisté gamblerství.<br>Rozpitváme pojmy jako <i>short squeeze</i>, margin call, likvidace pozice a ukážeme, jak rychle může ztráta přerůst původní vklad.<br>Projdeme si také základní risk management: velikost pozice, stop-loss, práce s pákou, diverzifikace a scénáře „co když to půjde proti mně o 10 / 20 / 50 %“.<br>Součástí budou modelové příklady na reálných grafech – bez doporučování konkrétních titulů, čistě vzdělávací pohled na mechaniku a riziko.</p><p>Na závěr jasně zdůrazníme, že shortování je pokročilá a vysoce riziková strategie, nevhodná pro začátečníky, a workshop neslouží jako osobní investiční doporučení, ale jako návod, <strong>jak chápat, co vlastně děláte, pokud se do shortů pouštíte</strong>.</p><p>&nbsp;</p>', '2026-10-18 17:30:00', 2, '/www/img/events/event_2_1763754251.jpg', 7, 'schváleno', NULL, '2025-11-21 20:44:11'),
(24, 4, 'Konzert Pokáče o kočce na náměstí', 200, '<p>Přijďte si se mnou zazpívat o kočce, vymlácených entrách nebo o tom jak je to v lese na prd.&nbsp;</p><p>Těším se na vás.</p>', '2026-03-11 19:00:00', 1, '/www/img/events/event_4_1763754389.jpg', 11, 'schváleno', NULL, '2025-11-21 20:46:29'),
(25, 4, 'Konzert Pokáče o kočce', 100, '<p>Přijďte si se mnou zazpívat o kočce, vymlácených entrách nebo o tom jak je to v lese na prd.&nbsp;</p><p>Těším se na vás.</p>', '2026-08-14 14:00:00', 1, '/www/img/events/event_4_1763754432.jpg', 10, 'schváleno', NULL, '2025-11-21 20:47:12'),
(26, 12, 'Festival jedlíků I', 30, '<p>Budou to jatka.</p>', '2026-12-12 16:00:00', 5, '/www/img/events/event_12_1763758044.png', 7, 'čeká se na schválení', NULL, '2025-11-21 21:47:24'),
(27, 12, 'Festival jedlíků II', 12, '<p>Budou to jatka.</p>', '2027-12-12 12:12:00', 5, '/www/img/events/event_12_1763758080.png', NULL, 'čeká se na schválení', NULL, '2025-11-21 21:48:00');

-- --------------------------------------------------------

--
-- Struktura tabulky `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

--
-- Vypisuji data pro tabulku `message`
--

INSERT INTO `message` (`id`, `name`, `email`, `message`) VALUES
(1, 'nezpokojený Honza', 'honzik@email.cz', 'Jak si můžete dovolit takto klamat lidi? Nikde jsem se na vašich stránkách nedozvěděl, že kurzy mají skvělé a pozitivní lektory. To nám prostě zatajujete!'),
(2, 'zvidavy Ondra', 'zvidaviOndra@email.cz', 'Dobrý den, rád bych se zeptal, zda nemáte kontakt na kapelu Kabát, kterou zde propagujete.\r\nPředem děkuji za odpověď,\r\nOndřej Soukup'),
(3, 'Alice Nováková', 'novakovaa@email.cz', 'Dobrý den, \r\nčím to je, že vaše webové stránky jsou jedna veliká nádhera? To jsem ještě nikdy neviděla. Město Plzeň se o své akce zas tak moc nestará a jsme moc rádi za přehledný výpis buduocích akcí.\r\nMějte se fajn, \r\nAlice');

-- --------------------------------------------------------

--
-- Struktura tabulky `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

--
-- Vypisuji data pro tabulku `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'Superadmin'),
(2, 'Admin'),
(3, 'Tvůrce'),
(4, 'Majitel prostoru');

-- --------------------------------------------------------

--
-- Struktura tabulky `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` int(1) NOT NULL,
  `information` text DEFAULT NULL,
  `photoPath` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

--
-- Vypisuji data pro tabulku `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `information`, `photoPath`) VALUES
(1, 'Administrátor', 'admin', '$2y$10$1bgNZrOT7LfuoxaWfl7/nOXgjhj21riPM9aYDSeIpq/TK.mF0pI7m', 1, NULL, '/www/img/users/user_1_1763746545.jpg'),
(2, 'Aleš Vávra', 'ales.vavra@email.cz', '$2y$10$xU0wOyzjs4RkTJmniIcuaucKkYsJBjN/O8YhJP8IfAEjlGwlTLOUq', 3, '<p>Bývalý portfolio manažer.&nbsp;<br>Po více než 10 letech byl hedgeový fond se strategií short only, kde jsem byl portfólio manažerem, uzavřen.</p>', '/www/img/users/user_2_1763733096.jpg'),
(3, 'Pert Novák', 'petr.novak@email.cz', '$2y$10$5pVTTx1EtCmvZg1XMKgy5Oih/T9C0iHNYcnU927WmcdiIWfbgicuO', 3, '<p>Serfuji na vlně života plný optimismu.&nbsp;</p><p>Rád se podělím o své dosavadní nasbírané know-how s těmi, kteří se nebojí naslouchat.</p>', '/www/img/users/user_3_1763747164.webp'),
(4, 'Pokáč', 'pokac@email.cz', '$2y$10$nXmA5xcMF9t6.dAtQjS4Bu1M7Z5LvjKz4SAxsTwc9bFIG5A/y4/XK', 3, '<p>Hraju si pro zábavu a lidem se to prý líbí.&nbsp;</p><p><br><strong>Příští konzerty:</strong></p><ul><li>Fórum Karlín</li><li>O2 Aréna</li><li>Bratislava</li><li>Nová Ves</li><li>Karlín</li><li>Brno</li><li>Plzeň</li></ul><p>Vstupenky zakoupíte na <a href=\"https://pokac.cz/\">https://pokac.cz/</a></p>', '/www/img/users/user_4_1763734154.jpg'),
(5, 'Kabát', 'kabat@email.cz', '$2y$10$4rDHdm1YuUjDA3L64rM6e.7kkI0s7QMq.SWBKuKNcDVgUAoJ9yHWC', 3, '<h4><a href=\"https://kabat.cz/chystame-turne-na-rok-2026/\">Chystáme Turné na rok 2026</a></h4><p><i>30.10.2025</i></p><p>Přátelé, jsme kapela, která miluje živé hraní, ale omezuje nás velikost té naší domoviny. Nechceme Vás nudit, a zároveň kapelu, jak se říká „ujezdit k smrti“. I tak jsme se rozhodli, že opět vyrazíme za Vámi do Vašich měst a domovů. Je to sice náročné, ale my si skoro dva měsíce zahrajeme a užíváme si turné se vším všudy.</p><p><strong>Těšíme se na vás!</strong></p>', '/www/img/users/user_5_1763734354.jpg'),
(6, 'Muzikál DJKT', 'muzikaldjkt@email.cz', '$2y$10$xMzETj5yuN3.Arp0MUgTkONJ1NeBTM6uoYqbF0.VA4EigbI7pd2gK', 3, '<p>&nbsp;</p><h4>Tanec léčí</h4><p>To asi všichni víme, ale co když se k němu přidá i hudba? A co když před vámi zěák začne dělat otočky a skupinový tance? To a nejen to na vás čeká každý večer na nové či malé scéně.</p><p>&nbsp;</p><p>Pro více informací navštivte:</p><p><a href=\"https://www.djkt.eu/muzikal\">https://www.djkt.eu/muzikal</a></p>', '/www/img/users/user_6_1763735001.png'),
(7, 'TechTower', 'techtower@email.cz', '$2y$10$4KiH53fzvMQ/ASGwoo.fVufkqoVvXxmUbzk3uK1dUYInrINRhgNka', 4, '<h4>Techtower je moderno</h4><p>TechTower je jedním z nejmodernějších technologických parků v České republice, který nabízí zázemí a kanceláře pro inovativní firmy, technologické nadšence, programátory a začínající podnikatele. Poskytuje coworkingové prostory, multifunkční sál, unikátní testovací vodní nádrž, jídelnu i SeedUp Space a v neposlední řadě prototypovou dílnu, kde se nápady mění ve skutečnost.</p><h4>Techtower je rodina</h4><p>TechTower je centrem, ve kterém se pořádají tradiční festivaly, nejrůznější konference, odborné workshopy, IoT akademie nebo hackathony. Na členy komunity čekají akce jako jsou společné snídaně, coffee breaky i blahodárná jóga.</p><p>&nbsp;</p><p>Přijďte se přesvědčit sami!</p><p><a href=\"https://www.techtower.cz/\">https://www.techtower.cz/</a></p>', '/www/img/users/user_7_1763735411.png'),
(8, 'Divadlo J. K. Tyla', 'djkt@email.cz', '$2y$10$VUp68/zwK7v5yGTXjzf3e.4HkFXuqxvntSDkeGfoEBGQyFq.GEn16', 4, '<p><strong>Divadlo J. K. Tyla</strong> je hlavní <a href=\"https://cs.wikipedia.org/wiki/Divadlo\">divadlo</a> v <a href=\"https://cs.wikipedia.org/wiki/Plze%C5%88sk%C3%BD_kraj\">Plzeňském kraji</a> a důležité kulturní centrum města <a href=\"https://cs.wikipedia.org/wiki/Plze%C5%88\">Plzně</a>. Má tři scény, <a href=\"https://cs.wikipedia.org/wiki/Velk%C3%A9_divadlo_(Plze%C5%88)\">Velké divadlo</a>, <a href=\"https://cs.wikipedia.org/wiki/Nov%C3%A9_divadlo_(Plze%C5%88)\">Nové divadlo</a> a Malou scénu (v budově Nového divadla). Novorenesanční divadlo patří mezi významné nemovité kulturní památky města. Kapacita sálu je 444 míst.</p>', '/www/img/users/user_8_1763735661.jpg'),
(9, 'Depo 2015', 'depo2015@email.cz', '$2y$10$zMl/AVoA/o1H/Y994Hk8buR/KEIeQWdWuDBjZJARraWtSfZ/gtgxu', 4, '<h3>Jsme prostor plný kultury<br>a kreativních nápadů</h3><p>DEPO2015 je živým prostorem, kde se propojuje kultura s byznysem.</p><p>Vzniklo v roce 2015, když byla Plzeň Evropským hlavním městem kultury.</p><p>Od této doby spojujeme inovativní myšlení, nové i tradiční technologie, kreativního ducha a lásku ke kultuře.</p><h4>Netradiční industriální prostory pro vaše akce!</h4><p>&nbsp;</p><p>web: <a href=\"https://www.depo2015.cz/\">https://www.depo2015.cz/</a>&nbsp;</p>', '/www/img/users/user_9_1763735819.png'),
(10, 'Měšťanská beseda', 'beseda@email.cz', '$2y$10$piJwHmQvcF4dPDpFVLt/9ejlxITxn37BagRzKDpNHDWq7CIsN8By6', 4, '<p><strong>Měšťanská beseda</strong> v Kopeckého sadech č. 13 (čp. 59) v <a href=\"https://cs.wikipedia.org/wiki/Plze%C5%88\">Plzni</a> byla dokončena v roce <a href=\"https://cs.wikipedia.org/wiki/1901\">1901</a> plzeňským stavitelem <a href=\"https://cs.wikipedia.org/wiki/Franti%C5%A1ek_Kotek\">Františkem Kotkem</a>. V architektonickém řešení budovy převládá <a href=\"https://cs.wikipedia.org/wiki/Novorenesance\">novorenesanční</a> sloh, umělecká výzdoba je převážně <a href=\"https://cs.wikipedia.org/wiki/Secese\">secesní</a>. Měšťanská beseda s několika společenskými sály a salonky a proslulou secesní kavárnou je jedno z nejvýznamnějších společensko-kulturních center v Plzni. Jedná se o třípodlažní budovu na obdélníkovém půdorysu zasahující hluboko do vnitrobloku, která je umístěna v souvislé zástavbě jižní strany Kopeckého sadů.</p>', '/www/img/users/user_10_1763745178.png'),
(11, 'Město Plzeň', 'mestoplzen@email.cz', '$2y$10$WuaUyAkE8MguX.aKd5GN4uQdY5xB5zig.XmzFn8/mgz9K9G6M2yTW', 4, '', '/www/img/users/user_11_1763745346.jpg'),
(12, 'Richard Levý', 'richard.levy@email.cz', '$2y$10$zptA4MiGi5aQIAWtJX46MOMjXNd4W2lBYfFKp2pEb/tqrkkofllJC', 3, '<p>Jsem člověk s citem pro auta, kterého baví kultura, sport a setkávání s lidmi.</p><p>Rád sleduji nové akce, organizuji vlastní projekty a hledám zajímavé prostory pro spolupráci.</p><p>Rád zkouším nové koncepty a nebojím se experimentovat.</p><p>Těším se na budoucí setkání na některém z mých akcí.</p>', '/www/img/users/user_12_1763746232.png'),
(13, 'Julie Pivničková', 'julie.pivnickova@email.cz', '$2y$10$nbc8GkZrgW8n8fmO3oI23O9FXCkG88zKychsYMncGkl3GP6cbd1Nq', 3, '<p>Jsem mladá žena, která miluje tanec a hudbu skoro v jakékoli podobě.</p><p>Nejčastěji trávím volný čas na tanečních lekcích, workshopech nebo na parketu s přáteli.</p><p>Tanec beru nejen jako zábavu, ale i jako způsob, jak se hýbat, vypnout hlavu a vyjádřit emoce.</p><p>Ráda zkouším nové taneční styly a učím se od lidí, kteří sdílí stejnou vášeň.</p><p>Hledám akce, kde se dá tančit, poznat nové lidi a užít si pohodovou atmosféru.</p>', '/www/img/users/user_13_1763746495.png'),
(14, 'Natálie Procházková', 'natalie.prochazkova@email.cz', '$2y$10$BYTbwmtEwjQ0Vbf3xhdI7OdSVOfGlU97US157bQys77s180KwjA2u', 2, NULL, '/www/img/users/user_14_1763746957.jpg'),
(15, 'Eliška Černá', 'eliska.cerna@email.cz', '$2y$10$I0yUdzG9UFpgdl1/kXg6eOsTHhjYNc7yIG8brYk8i41djA.yqm0gW', 2, NULL, '/www/img/users/user_15_1763746885.png'),
(16, 'Tomáš Svoboda', 'tomas.svoboda@email.cz', '$2y$10$HZjxUtKhW4mnsjvyGMgsvOfzhLG9N9zr/10KiU3Td1njrp4xMaUnS', 2, NULL, '/www/img/users/user_16_1763746817.jpg');

--
-- Indexy pro exportované tabulky
--

--
-- Indexy pro tabulku `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pro tabulku `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `place_id` (`place_id`);

--
-- Indexy pro tabulku `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pro tabulku `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pro tabulku `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role` (`role`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pro tabulku `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT pro tabulku `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pro tabulku `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pro tabulku `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `events_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`),
  ADD CONSTRAINT `events_ibfk_3` FOREIGN KEY (`place_id`) REFERENCES `users` (`id`);

--
-- Omezení pro tabulku `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
