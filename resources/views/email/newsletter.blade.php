<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        /* Styles de base pour Outlook */
        body {
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        /* Force Outlook à utiliser les bonnes marges */
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        /* Correction des images dans Outlook */
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        /* Styles responsives */
        @media only screen and (max-width: 480px) {
            table.mobile-hidden {
                max-width: 100% !important;
                width: 100% !important;
            }

            td[class="card-cell"] {
                display: block !important;
                width: 100% !important;
                padding: 0 0 16px 0 !important;
            }
        }
    </style>
    <!--[if mso]>
    <xml>
        <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->
</head>

<body style="margin:0; padding:0; background-color:#F4F5F6;">
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#F4F5F6">
    <tr>
        <td align="center" valign="top">
            <!-- Container principal 600px -->
            <table width="600" border="0" cellspacing="0" cellpadding="0" class="mobile-hidden">
                <!-- Header -->
                <tr>
                    <td align="center" valign="top" style="padding:20px 0;">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <!-- Logo ULB -->
                                <td width="50%" align="center">
                                    <img src="https://infofin.ulb.be/img/ulb_logo.png" width="250" alt="ULB Logo"
                                         style="display:block; max-width:250px;">
                                </td>
                                <!-- Titre Infofin -->
                                <td width="50%" align="center">
                                    <p style="font-family:Arial, sans-serif; font-size:32px; font-weight:700; color:#004C93; margin:0;">
                                        Infofin</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Contenu principal -->
                <tr>
                    <td bgcolor="#FFFFFF" style="padding:20px; border-radius:15px;">
                        <!-- Message de bienvenue -->
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="font-family:Arial, sans-serif; font-size:14px; line-height:20px; color:#000000; padding-bottom:10px;">
                                    Bonjour {{$data['prenom']}},
                                </td>
                            </tr>
                            <tr>
                                <td style="font-family:Arial, sans-serif; font-size:14px; line-height:20px; color:#000000; padding-bottom:20px;">
                                    Ces appels sont apparus la semaine dernière et pourraient vous intéresser ;
                                </td>
                            </tr>
                        </table>

                        <!-- Message informatif conditionnel -->
                        @if(!empty($data['message']))
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom:20px;">
                                <tr>
                                    <td bgcolor="lightskyblue" style="padding:10px;">
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td width="40" align="center" style="font-size:20px;">ℹ️</td>
                                                <td style="font-family:Arial, sans-serif; font-size:13px; line-height:18px;">
                                                    {!! $data['message'] !!}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        @endif

                        <!-- Structure des cartes -->
                        @php
                            $chunks = array_chunk($data['projects']->all(), 2);
                        @endphp

                        @foreach($chunks as $chunk)
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom:16px;">
                                <tr>
                                    @foreach($chunk as $card)
                                        <td width="50%" valign="top" class="card-cell" style="padding:0 8px;">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td style="border:2px solid #004C93; border-radius:5px; padding:8px;">
                                                        <!-- Titre du projet -->
                                                        <p style="color:#004C93; font-weight:700; font-family:Arial, sans-serif; font-size:14px; margin:0 0 8px 0;">
                                                            {{ strip_tags(htmlspecialchars_decode($card->title, ENT_HTML5))}}
                                                        </p>

                                                        <!-- Organisation et badge -->
                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0"
                                                               style="margin-bottom:8px;">
                                                            <tr>
                                                                <td style="font-family:Arial, sans-serif; font-size:12px; font-style:italic;">
                                                                    {{strip_tags(htmlspecialchars_decode($card->organisation->title, ENT_HTML5))}}
                                                                </td>
                                                                <td align="right">
                                    <span
                                        style="background:rgba(39,109,32,0.10); padding:2px 6px; border-radius:4px; color:#276D20; font-size:10px; font-weight:600;">
                                        Financement
                                    </span>
                                                                </td>
                                                            </tr>
                                                        </table>

                                                        <!-- Deadline -->
                                                        <p style="font-family:Arial, sans-serif; font-size:11px; font-style:italic; font-weight:600; margin:0 0 8px 0;">
                                                            Prochaine deadline: {{$card->firstDeadline}}
                                                        </p>

                                                        <!-- Description -->
                                                        <p style="font-family:Arial, sans-serif; font-size:12px; line-height:16px; margin:0 0 12px 0;">
                                                            {{ strip_tags(htmlspecialchars_decode(mb_substr($card->short_description, 0, 200, 'UTF-8'), ENT_HTML5)) }}

                                                            ...
                                                        </p>

                                                        <!-- Bouton -->
                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                                <td align="right">
                                                                    <table border="0" cellspacing="0" cellpadding="0">
                                                                        <tr>
                                                                            <td bgcolor="#276D20"
                                                                                style="padding:6px 20px; border-radius:5px;">
                                                                                <a href="{{'https://infofin.ulb.be/projects/' . $card->id . "?from_email=true"}}"
                                                                                   style="color:white; font-family:Arial, sans-serif; font-size:12px; text-decoration:none; display:block;">
                                                                                    En savoir plus
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>

                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    @endforeach

                                    @if(count($chunk) === 1)
                                        <td width="50%" valign="top" class="card-cell" style="padding:0 8px;">
                                            &nbsp;
                                        </td>
                                    @endif
                                </tr>
                            </table>
                        @endforeach

                        <!-- Footer -->
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td align="right"
                                    style="padding-top:20px; font-family:Arial, sans-serif; font-size:12px; color:#9A9EA6;">
                                    Université libre de Bruxelles, 1050 Bruxelles<br/>
                                    Plus envie de recevoir ce mail ? <a href="{{$data['url']}}" style="color:#9A9EA6;">Se
                                        désabonner.</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
