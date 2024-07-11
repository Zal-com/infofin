<?php

namespace App\Http\Controllers;
use phpCAS;

class CAS extends Controller
{
    public function index()
    {
        phpCAS::setVerbose(true);
        phpCAS::client(SAML_VERSION_1_1, 'auth-pp.ulb.be', 443, 'https://infofin-f-departementrecherche.apps.dev.okd.hpda.ulb.ac.be', '');
        //phpCAS::forceAuthentication();
    }
}
