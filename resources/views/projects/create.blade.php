@extends('layout')
@section('content')
<div>
    <!-- I begin to speak only when I am certain what I will say is not better left unsaid. - Cato the Younger -->
Project create
    <form>
        <div>
            <input type="text" placeholder="Programme" name="Name" required/>
            <input type="text" placeholder="Organisation" name="Organisation" required/>
            <div class="border border-blue-900 p-2">
                <h3>Deadlines</h3>
                <div class="border-blue-900 border p-2">
                    <input type="datetime-local" name="Deadline">
                    <input type="checkbox" name="Continu1">
                    <label for="Continu1">Continu</label>
                    <label for="deadline_type">Type de deadline</label>
                    <select name="deadline_type">
                        <option selected disabled hidden>---</option>
                        <option value="Deposit of the draft">Deposit of the draft</option>
                        <option value="Deposit of the letter of intent">Deposit of the letter of intent</option>
                        <option value="Deposit of the preliminary draft">Deposit of the preliminary draft</option>
                        <option value="Full-proposal">Full-proposal</option>
                        <option value="Information session">Information session</option>
                        <option value="Internal deadline">Internal deadline</option>
                        <option value="Pre-proposal">Pre-proposal</option>
                        <option value="Promoter">Promoter</option>
                        <option value="Registration">Registration</option>
                        <option value="Submission of applications">Submission of applications</option>
                    </select>
                </div>
                <div class="border-blue-900 border p-2">
                    <input type="datetime-local" name="Deadline2">
                    <input type="checkbox" name="Continu2">
                    <label for="Continu2">Continu</label>
                    <label for="deadline2_type">Type de deadline</label>
                    <select name="deadline2_type">
                        <option selected disabled hidden>---</option>
                        <option value="Deposit of the draft">Deposit of the draft</option>
                        <option value="Deposit of the letter of intent">Deposit of the letter of intent</option>
                        <option value="Deposit of the preliminary draft">Deposit of the preliminary draft</option>
                        <option value="Full-proposal">Full-proposal</option>
                        <option value="Information session">Information session</option>
                        <option value="Internal deadline">Internal deadline</option>
                        <option value="Pre-proposal">Pre-proposal</option>
                        <option value="Promoter">Promoter</option>
                        <option value="Registration">Registration</option>
                        <option value="Submission of applications">Submission of applications</option>
                    </select>
                </div>
            </div>

            <div>
            <label for="DateBailleur">Date Bailleur</label>
            <input type="datetime-local" name="DateBailleur">
        </div>
            <div>
            <input type="checkbox" name="GProj">
            <label for="GProj">Projet majeur</label>
        </div>
            <div>
            <label for="type_info">Type d'information</label>
            <select name="type_info" required>
                <option selected disabled hidden>---</option>
                <option value="1">Financement</option>
                <option value="2">Séance d'information organisée par l'ULB</option>
                <option value="3">Séance d'information organisée par un organisme externe</option>
            </select>
        </div>
            <div>
                <label for="type_programme">Type de programme</label>
                <select multiple name="type_programme" required>
                    <option selected disabled hidden>---</option>
                    <option>Appels à projets de recherche fondamentale/ou de base (hors financement de l’Union Européenne)</option>
                    <option>Appels à projets financés par l’Union Européenne</option>
                    <option>Appels à projets de coopération au développement</option>
                    <option>Appels à projets de collaboration internationale</option>
                    <option>Transfert technologique et valorisation de la recherche</option>
                    <option>Financement de thèse de doctorat</option>
                    <option>Financement de post-doctorat</option>
                    <option>Consultance, expertise et marchés publics</option>
                    <option>Financement de colloques, congres et publications</option>
                    <option>Financement de mobilité « In » (venir à l’ULB)</option>
                    <option>Financement de mobilité « OUT » (partir à l’extérieur de l’ULB)</option>
                    <option>Financement d’activités de networking</option>
                    <option>Programmes de support à l'enseignement</option>
                    <option>Prix et distinctions</option>
                    <option>Séances d'information ou de formation relatives au financement à l’ULB</option>
                    <option>Séances d'information ou de formation relatives au financement à l’extérieur de l’ULB</option>
                    <option>Recrutement de personnel</option>
                    <option>Médiation scientifique</option>
                </select>
            </div>
            <div>
                <label for="disc_sci">Disciplines scientifiques de l'appel</label>
                <select multiple>
                    <optgroup label="Sciences de la vie">
                        <option>Dentisterie</option>
                        <option>Médecine</option>
                        <option>Médecine vétérinaire</option>
                        <option>Neurosciences</option>
                        <option>Pharmacie</option>
                        <option>Santé publique</option>
                        <option>Sciences biomédicales</option>
                        <option>Sciences de la motricité</option>
                    </optgroup>
                    <optgroup label="Sciences exactes et appliquées">
                        <option>Agronomie</option>
                        <option>Architecture / Sciences de l'habitat / Urbanisme</option>
                        <option>Biologie / Bioingénieurie</option>
                        <option>Chimie / Sciences des matériaux</option>
                        <option>Environnement / Aménagement du territoire / Développement durable</option>
                        <option>Géographie</option>
                        <option>Géologie</option>
                        <option>Mathématiques et statistiques</option>
                        <option>Physique</option>
                        <option>Sciences de l'ingénieur</option>
                        <option>TIC et informatique</option>
                        <option>Tourisme</option>
                    </optgroup>
                    <optgroup label="Sciences humaines et sociales">
                        <option>Arts et archéologie</option>
                        <option>Démographie</option>
                        <option>Droit et criminologie</option>
                        <option>Economie / Gestion</option>
                        <option>Etudes européennes</option>
                        <option>Géographie humaine</option>
                        <option>Histoire</option>
                        <option>Information / Communication</option>
                        <option>Linguistique, langues et littérature</option>
                        <option>Philosophie</option>
                        <option>Psychologie / Sciences cognitives</option>
                        <option>Sciences de l'Education</option>
                        <option>Sciences des religions</option>
                        <option>Sciences politiques / Relations internationales</option>
                        <option>Sociologie / Anthropologie / Sciences du travail</option>
                    </optgroup>
                </select>
            </div>
            <div>
                <h3>Descriptions</h3>
                <div>
                    <label for="short_desc">Description courte</label>
                    <div id="editor_short" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></div>
                </div>
                <div>
                    <label for="long_desc">Description complète</label>
                    <div id="editor_long" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
