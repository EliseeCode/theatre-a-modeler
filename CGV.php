<?php
/* Main page with two forms: sign up and log in */
require 'db.php';
session_start();


?>
<!DOCTYPE html>
<html lang="fr">
  <head>
  <?php include 'css/css.html'; ?>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CGV/CGU</title>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
	  <link href="css/main.css?v=2" rel="stylesheet">
    <link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="img/favicon-16x16.png" sizes="16x16" />
    <script src='js/cookiesManager.js'></script>
    <style>
      .navbar{
        margin-bottom:0;
        border-radius:0;
      }
    </style>
</head>
  <body class="fond">
    <!-- JUMBOTRON -->
    <div class="jumbotron text-center">
      <div class="container">

        <h1><span class="titre1" style="font-weight:normal">ExoLingo</span></h1>

	</div>
    </div>

  <pre style="width: 800px;margin: auto;max-width:100%;">
  <h1>Privacy Policy</h1>
  Your privacy is critically important to us.
  k-pragmatik
  (adresse à ajouter)
  It is ExoLingo's policy to respect your privacy regarding any information we may collect while
  operating our website. This Privacy Policy applies to https://ExoLingo.com and all subdomains of
  ExoLingo.com. (hereinafter, "us", "we", or "https://ExoLingo.com"). We respect your privacy and are
  committed to protecting personally identifiable information you may provide us through the
  Website. We have adopted this privacy policy ("Privacy Policy") to explain what information may
  be collected on our Website, how we use this information, and under what circumstances we
  may disclose the information to third parties. This Privacy Policy applies only to information we
  collect through the Website and does not apply to our collection of information from other
  sources.
  This Privacy Policy, together with the Terms and conditions posted on our Website, set forth the
  general rules and policies governing your use of our Website. Depending on your activities
  when visiting our Website, you may be required to agree to additional terms and conditions.
  Website Visitors
  Like most website operators, ExoLingo collects non-personally-identifying information of the
  sort that web browsers and servers typically make available, such as the browser type,
  language preference, referring site, and the date and time of each visitor request. ExoLingo's
  purpose in collecting non-personally identifying information is to better understand how ExoLingo
  ApS's visitors use its website. From time to time, ExoLingo may release
  non-personally-identifying information in the aggregate, e.g., by publishing a report on trends in
  the usage of its website.
  ExoLingo also collects potentially personally-identifying information like Internet Protocol (IP)
  addresses for logged in users and for users leaving comments on https://ExoLingo.com blog
  posts. ExoLingo only discloses logged in user and commenter IP addresses under the same
  circumstances that it uses and discloses personally-identifying information as described below.
  Gathering of Personally-Identifying Information
  Certain visitors to ExoLingo's websites choose to interact with ExoLingo in ways that
  require ExoLingo to gather personally-identifying information. The amount and type of
  information that ExoLingo gathers depends on the nature of the interaction. For example, we
  ask visitors who sign up for a blog at https://ExoLingo.com to provide a username and email
  address.
  Security
  The security of your Personal Information is important to us, but remember that no method of
  transmission over the Internet, or method of electronic storage is 100% secure. While we strive
  to use commercially acceptable means to protect your Personal Information, we cannot
  guarantee its absolute security.
  <h2>Links To External Sites</h2>
  Our Service may contain links to external sites that are not operated by us. If you click on a third
  party link, you will be directed to that third party's site. We strongly advise you to review the
  Privacy Policy and terms and conditions of every site you visit.
  We have no control over, and assume no responsibility for the content, privacy policies or
  practices of any third party sites, products or services.
  Https://ExoLingo.com uses Google AdWords for
  remarketing
  Https://ExoLingo.com uses the remarketing services to advertise on third party websites (including
  Google) to previous visitors to our site. It could mean that we advertise to previous visitors who
  haven't completed a task on our site, for example using the contact form to make an enquiry.
  This could be in the form of an advertisement on the Google search results page, or a site in the
  Google Display Network. Third-party vendors, including Google, use cookies to serve ads based
  on someone's past visits. Of course, any data collected will be used in accordance with our own
  privacy policy and Google's privacy policy.
  You can set preferences for how Google advertises to you using the Google Ad Preferences
  page, and if you want to you can opt out of interest-based advertising entirely by cookie settings
  or permanently using a browser plugin.
  <h2>Protection of Certain Personally-Identifying Information</h2>
  ExoLingo discloses potentially personally-identifying and personally-identifying information
  only to those of its employees, contractors and affiliated organizations that (i) need to know that
  information in order to process it on ExoLingo's behalf or to provide services available at
  ExoLingo's website, and (ii) that have agreed not to disclose it to others. Some of those
  employees, contractors and affiliated organizations may be located outside of your home
  country; by using ExoLingo's website, you consent to the transfer of such information to
  them. ExoLingo will not rent or sell potentially personally-identifying and
  personally-identifying information to anyone. Other than to its employees, contractors and
  affiliated organizations, as described above, ExoLingo discloses potentially
  personally-identifying and personally-identifying information only in response to a subpoena,
  court order or other governmental request, or when ExoLingo believes in good faith that
  disclosure is reasonably necessary to protect the property or rights of ExoLingo, third parties
  or the public at large.
  If you are a registered user of https://ExoLingo.com and have supplied your email address,
  ExoLingo may occasionally send you an email to tell you about new features, solicit your
  feedback, or just keep you up to date with what's going on with ExoLingo and our products.
  You can opt out of any and all email activities on the platform as per described by GDPR. We
  primarily use our blog to communicate this type of information, so we expect to keep this type of
  email to a minimum. If you send us a request (for example via a support email or via one of our
  feedback mechanisms), we reserve the right to publish it in order to help us clarify or respond to
  your request or to help us support other users. ExoLingo takes all measures reasonably
  necessary to protect against the unauthorized access, use, alteration or destruction of
  potentially personally-identifying and personally-identifying information.
  <h2>Aggregated Statistics</h2>
  ExoLingo may collect statistics about the behavior of visitors to its website. ExoLingo may
  display this information publicly or provide it to others. However, ExoLingo does not disclose
  your personally-identifying information.
  <h2>Affiliate Disclosure</h2>
  ExoLingo uses affiliate links and does earn a commission from certain links. This does not affect
  your purchases or the price you may pay.
  <h2>Cookies</h2>
  To enrich and perfect your online experience, ExoLingo uses "Cookies", similar technologies
  and services provided by others to display personalized content, appropriate advertising and
  store your preferences on your computer.
  A cookie is a string of information that a website stores on a visitor's computer, and that the
  visitor's browser provides to the website each time the visitor returns. ExoLingo uses cookies
  to help ExoLingo identify and track visitors, their usage of https://ExoLingo.com, and their
  website access preferences. ExoLingo visitors who do not wish to have cookies placed on
  their computers should set their browsers to refuse cookies before using ExoLingo's
  websites, with the drawback that certain features of ExoLingo's websites may not function
  properly without the aid of cookies.
  By continuing to navigate our website without changing your cookie settings, you hereby
  acknowledge and agree to ExoLingo's use of cookies.
  <h2>E-commerce</h2>
  Those who engage in transactions with ExoLingo – by purchasing ExoLingo's services or
  products, are asked to provide additional information, including as necessary the personal and
  financial information required to process those transactions. In each case, ExoLingo collects
  such information only insofar as is necessary or appropriate to fulfill the purpose of the visitor's
  interaction with ExoLingo. ExoLingo does not disclose personally-identifying information.
  And visitors can always refuse to supply personally-identifying information, with the caveat that
  it may prevent them from engaging in certain website-related activities.
  <h2>Business Transfers</h2>
  If ExoLingo, or substantially all of its assets, were acquired, or in the unlikely event that
  ExoLingo goes out of business or enters bankruptcy, user information would be one of the
  assets that is transferred or acquired by a third party. You acknowledge that such transfers may
  occur, and that any acquirer of ExoLingo may continue to use your personal information as
  set forth in this policy.
  <h2>GDPR Privacy Notice</h2>
  General Data Protection Regulation (GDPR)
  Article 13 of Regulation EU 2016/679
  <h3>1. Purpose of this notice</h3>
  This Privacy Notice provides mandatory information as required under Articles 13 and 14 of the
  European General Data Protection Regulation (GDPR) regarding the transparency of personal
  data processing. Definitions of certain terms within this notice are explained in the appendix.
  <h3>2. The Data Controller for personal data</h3>
  The Data Controller for the personal data processed by us is the Client Company of ExoLingo
  (the employer of the natural person whose data is collected, hereafter referred to as the Data
  Subject). The Data Controller will pass personal data of their users to ExoLingo to manage
  education on behalf of those users in connection with their business. ExoLingo, as Data
  Processor acting on the instructions of the Data Controller under a written contract with them,
  will subsequently use that personal data to facilitate education for the Data Subject. It is this
  contract which forms the ‘Legal Basis’ for the processing of personal data carried out by ExoLingo
  in these circumstances.
  ExoLingo will also become a Data Controller if it collects additional personal data directly from a
  Data Subject. In these circumstances ExoLingo will be acting under a ‘Legitimate Interest’ to
  legally process the data for the management of education for the Data Subject and to fulfil the
  contractual requirements for its Client. ExoLingo also acts as a Data Controller for any personal
  data held regarding its own employees, and legally processes this data under its Contract of
  Employment with those Data Subjects.
  <h3>3. Your Rights</h3>
  As a Data Subject you have rights under the GDPR. These rights can be seen below. ExoLingo
  will always fully respect your rights regarding the processing of your personal data, and has
  provided below the details of the person to contact if you have any concerns or questions
  regarding how we process your data, or if you wish to exercise any rights you have under the
  GDPR.
  <h3>4. Contact Details</h3>
  The identity and contact details for the Data Protection Officer within ExoLingo is:
  Andreas Piculell, Chief Engineering Officer
  ExoLingo
  (Adresse à ajouter)
  <h3>5. Data Protection Principles</h3>
  ExoLingo has adopted the following principles to govern its collection and processing of Personal
  Data:
  Personal Data shall be processed lawfully, fairly, and in a transparent manner.
  The Personal Data collected will only be those specifically required to fulfil education or other
  education-related requirements. Such data may be collected directly from the Data Subject or
  provided to ExoLingo via his /her institution. Such data will only be processed for that purpose.
  Personal Data shall only be retained for as long as it is required to fulfil contractual
  requirements, or to provide statistics to our Client Company.
  Personal Data shall be adequate, relevant, and limited to what is necessary in relation to the
  purposes for which they are collected and/or processed. Personal Data shall be accurate and,
  where necessary, kept up to date.
  The Data Subject has the right to request from ExoLingo access to and rectification or erasure of
  their personal data, to object to or request restriction of processing concerning the data, or to
  the right to data portability. In each case such a request must be put in writing as in Section 3
  above.
  The Data Subject has the right to make a complaint directly to a supervisory authority within
  their own country. ExoLingo’s Data Protection compliance is supervised by:
  <p>CNIL
  Commission Nationale de l'Informatique et des Libertés
  3 Place de Fontenoy
  TSA 80715
  75334 PARIS CEDEX 07
  France
  Tel: +33 (0)1.53.73.22.22
  Fax: +33 (0)1.53.73.22.00</p>
  Personal Data shall only be processed based on the legal basis explained in section 2 above,
  except where such interests are overridden by the fundamental rights and freedoms of the Data
  Subject which will always take precedent. If the Data Subject has provided specific additional
  Consent to the processing, then such consent may be withdrawn at any time (but may then
  result in an inability to fulfil educational requirements).
  <h3>6. Transfers to Third Parties</h3>
  To fulfil the educational arrangements for a Data Subject it will in most cases be necessary to
  process personal data via a third party (these will include but are not limited to bug reporting,
  customer support, and credit card companies). Personal Data shall only be transferred to, or
  processed by, third party companies where such companies are necessary for the fulfilment of
  the educational arrangements.
  Personal Data shall not be transferred to a country or territory outside the European Economic
  Area (EEA) unless the transfer is made to a country or territory recognised by the EU as having
  an adequate level of Data Security, or is made with the consent of the Data Subject, or is made
  to satisfy the Legitimate Interest of ExoLingo in regard to its contractual arrangements with its
  clients.
  All internal group transfers of Personal Data shall be subject to written agreements under the
  Company’s Intra Group Data Transfer Agreement (IGDTA) for internal Data transfers which are
  based on Standard Contractual Clauses recognised by the European Data Protection Authority.
  Appendix – Definitions of certain terms referred to above:
  Personal Data:
  (Article 4 of the GDPR): ‘personal data’ means any information relating to an identified or
  identifiable natural person (‘data subject’); an identifiable natural person is one who can be
  identified, directly or indirectly, in particular by reference to an identifier such as a name, an
  identification number, location data, an online identifier or to one or more factors specific to the
  physical, physiological, genetic, mental, economic, cultural or social identity of that natural
  person.
  Processing:
  (Article 4 of the GDPR): means any operation or set of operations which is performed upon
  personal data or sets of personal data, whether or not by automated means, such as collection,
  recording, organization, structuring, storage, adaptation or alteration, retrieval, consultation, use,
  disclosure by transmission, dissemination or otherwise making available, alignment or
  combination, erasure or destruction.
  Legal Basis for Processing:
  (Article 6 of the GDPR): At least one of these must apply whenever personal data is processed:
  1. Consent: the individual has given clear consent for the processing of their personal data
  for a specific purpose.
  2. Contract: the processing is necessary for compliance with a contract.
  3. Legal obligation: the processing is necessary to comply with the law (not including
  contractual obligations).
  4. Vital interests: the processing is necessary to protect someone’s life.
  5. Public task: the processing is necessary to perform a task in the public interest, and the
  task or function has a clear basis in law.
  6. Legitimate interests: the processing is necessary for the legitimate interests of the Data
  Controller unless there is a good reason to protect the individual’s personal data which
  overrides those legitimate interests.
  Data Controller:
  (Article 4 of the GDPR): this means the person or company that determines the purposes and
  the means of processing personal data.
  Data Processor:
  (Article 4 of the GDPR): means a natural or legal person, public authority, agency or any other
  body which processes personal data on behalf of the controller.
  Data Subject Rights:
  (Chapter 3 of the GDPR) each Data Subject has eight rights. These are:
  1. The right to be informed; This means anyone processing your personal data must make
  clear what they are processing, why, and who else the data may be passed to.
  2. The right of access; this is your right to see what data is held about you by a Data
  Controller.
  3. The right to rectification; the right to have your data corrected or amended if what is held
  is incorrect in some way.
  4. The right to erasure; under certain circumstances you can ask for your personal data to
  be deleted. This is also called ‘the Right to be Forgotten’. This would apply if the
  personal data is no longer required for the purposes it was collected for, or your consent
  for the processing of that data has been withdrawn, or the personal data has been
  unlawfully processed.
  5. The right to restrict processing; this gives the Data Subject the right to ask for a
  temporary halt to processing of personal data, such as in the case where a dispute or
  legal case has to be concluded, or the data is being corrected.
  6. The right to data portability; a Data Subject has the right to ask for any data supplied
  directly to the Data Controller by him or her, to be provided in a structured, commonly
  used, and machine-readable format.
  7. The right to object; the Data Subject has the right to object to further processing of their
  data which is inconsistent with the primary purpose for which it was collected, including
  profiling, automation, and direct marketing.
  8. Rights in relation to automated decision making and profiling; Data Subjects have the
  right not to be subject to a decision based solely on automated processing.
  <h2>Privacy Policy Changes</h2>
  Although most changes are likely to be minor, ExoLingo may change its Privacy Policy from
  time to time, and in ExoLingo's sole discretion. ExoLingo encourages visitors to frequently
  check this page for any changes to its Privacy Policy. Your continued use of this site after any
  change in this Privacy Policy will constitute your acceptance of such change.<pre>

</body>

</html>
