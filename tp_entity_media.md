# TP - Création d'une Entity Media pour CodeIgniter 4

## Objectifs pédagogiques
- Comprendre le rôle d'une Entity dans CodeIgniter 4
- Créer une Entity pour gérer les médias de manière orientée objet
- Manipuler les attributs et méthodes d'une Entity
- Améliorer la gestion du modèle MediaModel existant

## Prérequis
- Connaissance de base des Entities CodeIgniter 4
- Compréhension du modèle MVC
- Notions de PHP orienté objet

---

## Introduction : Rappel sur les Entities

Une **Entity** en CodeIgniter 4 est une classe qui représente une ligne de données d'une table. Elle permet de :
- Encapsuler les données et leur logique métier
- Convertir automatiquement les types de données
- Créer des méthodes métier spécifiques
- Améliorer la maintenabilité du code

**Exemple** : Au lieu de manipuler un tableau `$user['password']`, on utilise `$user->setPassword('...')` avec validation intégrée.

---

## Partie 1 : Analyse du contexte

### Question 1.1 📋
Observez le fichier `MediaModel.php` fourni. Identifiez :
1. Quel est le `$returnType` actuel du modèle ?
2. Quels sont les champs de la table `media` (`$allowedFields`) ?
3. Quels sont les champs de type date (`useTimestamps`) ?

<details>
<summary>✅ Réponse</summary>

1. **$returnType actuel** : `'array'` - Les résultats sont retournés sous forme de tableaux associatifs
2. **Champs de la table** : `['file_path', 'entity_id', 'entity_type', 'title', 'alt']`
3. **Champs de date** : `created_at`, `updated_at`, `deleted_at` (grâce à `useTimestamps = true`)

</details>

---

### Question 1.2 🤔
D'après vous, pourquoi serait-il intéressant de créer une Entity pour les médias plutôt que de continuer à utiliser des tableaux ?

<details>
<summary>💡 Réponse attendue</summary>

**Avantages d'une Entity Media** :
- **Typage fort** : Garantit que `entity_id` est toujours un entier
- **Méthodes métier** : Créer des méthodes comme `getFullPath()`, `isImage()`, `delete()`
- **Validation** : Vérifier automatiquement que `entity_type` est valide
- **Conversion automatique** : Les dates deviennent des objets `Time` au lieu de chaînes
- **Code plus lisible** : `$media->getUrl()` au lieu de `base_url($media['file_path'])`

</details>

---

## Partie 2 : Création du squelette de l'Entity

### Étape 2.1 : Créer le fichier

📁 Créez le fichier `app/Entities/Media.php`

```php
<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Media extends Entity
{
    // Nous allons compléter cette classe étape par étape
}
```

### Question 2.1 ❓
Pourquoi doit-on étendre la classe `CodeIgniter\Entity\Entity` ?

<details>
<summary>✅ Réponse</summary>

On étend `Entity` pour bénéficier de :
- La gestion automatique des getters/setters
- La conversion de types (`$casts`)
- La protection contre la modification de certains champs
- Les méthodes utilitaires (`toArray()`, `fill()`, etc.)

</details>

---

## Partie 3 : Définir les attributs

### Étape 3.1 : Déclarer les attributs

Ajoutez la propriété `$attributes` dans votre classe :

```php
protected $attributes = [
    'id'          => null,
    'file_path'   => null,
    'entity_id'   => null,
    'entity_type' => null,
    'title'       => null,
    'alt'         => null,
    'created_at'  => null,
    'updated_at'  => null,
    'deleted_at'  => null,
];
```

### Question 3.1 🧐
Pourquoi initialise-t-on tous les attributs à `null` ?

<details>
<summary>✅ Réponse</summary>

- **Documentation** : Cela documente tous les champs disponibles
- **Valeurs par défaut** : Évite les erreurs si un champ n'est pas défini dans la BDD
- **IDE** : Permet à l'IDE de suggérer les propriétés disponibles
- **Cohérence** : Garantit que toutes les instances ont la même structure

</details>

---

### Étape 3.2 : Définir les conversions de types

Ajoutez la propriété `$casts` :

```php
protected $casts = [
    'id'          => 'integer',
    'entity_id'   => 'integer',
    'entity_type' => 'string',
    'file_path'   => 'string',
    'title'       => 'string',
    'alt'         => 'string',
    'created_at'  => 'datetime',
    'updated_at'  => 'datetime',
    'deleted_at'  => 'datetime',
];
```

### Question 3.2 🔍
Que se passe-t-il concrètement lorsqu'on définit `'created_at' => 'datetime'` ?

<details>
<summary>✅ Réponse</summary>

**Conversion automatique** :
- En **lecture** : La chaîne `"2025-10-14 10:30:00"` devient un objet `CodeIgniter\I18n\Time`
- En **écriture** : Un objet `Time` est converti en chaîne pour la BDD

**Exemple pratique** :
```php
// Sans cast
$media['created_at'] = "2025-10-14 10:30:00";
echo $media['created_at']; // "2025-10-14 10:30:00"

// Avec cast
$media->created_at; // Objet Time
echo $media->created_at->humanize(); // "il y a 2 heures"
```

</details>

---

### Étape 3.3 : Définir les dates et champs cachés

Ajoutez :

```php
protected $dates = ['created_at', 'updated_at', 'deleted_at'];
```

### Question 3.3 ❓
Quelle est la différence entre `$casts` et `$dates` pour les champs de type date ?

<details>
<summary>💡 Réponse</summary>

- **`$casts`** : Définit le **type** de conversion (datetime, date, timestamp...)
- **`$dates`** : Liste les champs qui doivent être **automatiquement convertis** même sans cast explicite

**Bonne pratique** : Utiliser les deux pour plus de clarté et de compatibilité.

</details>

---

## Partie 4 : Créer des méthodes métier

### Étape 4.1 : Méthode pour obtenir l'URL complète

Ajoutez cette méthode :

```php
/**
 * Retourne l'URL complète du fichier média
 */
public function getUrl(): string
{
    return base_url($this->file_path);
}
```

### Question 4.1 🎯
Quel est l'avantage de créer cette méthode plutôt que d'écrire `base_url($media['file_path'])` partout dans le code ?

<details>
<summary>✅ Réponse</summary>

**Avantages** :
1. **Centralisation** : Si la logique change (ex: CDN externe), on modifie un seul endroit
2. **Lisibilité** : `$media->getUrl()` est plus explicite
3. **Maintenance** : Évite la duplication de code
4. **Testabilité** : Plus facile à mocker pour les tests

**Exemple d'évolution** :
```php
public function getUrl(): string
{
    // Si on passe à un CDN plus tard
    return "https://cdn.monsite.com/" . $this->file_path;
}
```

</details>

---

### Étape 4.2 : Méthode pour vérifier le type de média

Ajoutez :

```php
/**
 * Vérifie si le média est une image
 */
public function isImage(): bool
{
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    $extension = pathinfo($this->file_path, PATHINFO_EXTENSION);
    return in_array(strtolower($extension), $imageExtensions);
}
```

### Question 4.2 🖼️
Créez une méthode similaire `getFileExtension()` qui retourne l'extension du fichier en minuscules.

<details>
<summary>✅ Réponse</summary>

```php
/**
 * Retourne l'extension du fichier en minuscules
 */
public function getFileExtension(): string
{
    return strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));
}
```

**Bonus** : On peut maintenant améliorer `isImage()` :
```php
public function isImage(): bool
{
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    return in_array($this->getFileExtension(), $imageExtensions);
}
```

</details>

---

### Étape 4.3 : Méthode pour vérifier la validité de l'entity_type

Ajoutez une méthode de validation :

```php
/**
 * Vérifie si le type d'entité est valide
 */
public function isValidEntityType(): bool
{
    $validTypes = ['user', 'recipe', 'recipe_mea', 'step', 'ingredient', 'brand'];
    return in_array($this->entity_type, $validTypes);
}
```

### Question 4.3 🔒
Pourquoi ne pas simplement s'appuyer sur les règles de validation du modèle ? Quel est l'intérêt d'avoir cette méthode dans l'Entity ?

<details>
<summary>💡 Réponse</summary>

**Différence de responsabilité** :
- **Validation du Model** : S'applique lors de l'**insertion/mise à jour** en BDD
- **Méthode d'Entity** : Permet de vérifier la cohérence **à tout moment** dans le code

**Cas d'usage** :
```php
// Avant d'afficher un média chargé depuis cache ou API externe
if ($media->isValidEntityType()) {
    echo $media->getUrl();
} else {
    log_message('error', 'Type d\'entité invalide : ' . $media->entity_type);
}
```

**Avantage** : Validation métier indépendante de la couche base de données.

</details>

---

### Étape 4.4 : Méthode pour obtenir le chemin absolu

```php
/**
 * Retourne le chemin absolu du fichier sur le serveur
 */
public function getAbsolutePath(): string
{
    return FCPATH . $this->file_path;
}

/**
 * Vérifie si le fichier existe physiquement
 */
public function fileExists(): bool
{
    return file_exists($this->getAbsolutePath());
}
```

### Question 4.4 💾
Imaginez un scénario où `fileExists()` retourne `false` alors que l'enregistrement existe en base de données. Que pourrait-il s'être passé ?

<details>
<summary>🤔 Scénarios possibles</summary>

1. **Suppression manuelle** : Le fichier a été supprimé du serveur sans passer par l'application
2. **Migration ratée** : Lors d'un déploiement, les fichiers n'ont pas été copiés
3. **Corruption** : Problème de permissions ou d'espace disque
4. **Path erroné** : `file_path` stocké incorrectement en base

**Action recommandée** :
```php
if (!$media->fileExists()) {
    log_message('error', "Fichier manquant : {$media->file_path}");
    // Optionnel : supprimer l'entrée en BDD ou marquer comme "orphelin"
}
```

</details>

---

## Partie 5 : Méthodes avancées

### Étape 5.1 : Obtenir des informations sur le fichier

```php
/**
 * Retourne la taille du fichier en octets (ou false si inexistant)
 */
public function getFileSize(): int|false
{
    if (!$this->fileExists()) {
        return false;
    }
    return filesize($this->getAbsolutePath());
}

/**
 * Retourne la taille du fichier formatée (ex: "1.5 MB")
 */
public function getFormattedFileSize(): string
{
    $size = $this->getFileSize();
    
    if ($size === false) {
        return 'N/A';
    }
    
    $units = ['o', 'Ko', 'Mo', 'Go'];
    $power = $size > 0 ? floor(log($size, 1024)) : 0;
    
    return round($size / pow(1024, $power), 2) . ' ' . $units[$power];
}
```

### Question 5.1 💾
À quoi sert la méthode `getFormattedFileSize()` ? Donnez un exemple d'utilisation dans une interface utilisateur.

<details>
<summary>✅ Utilisation pratique</summary>

**Utilité** : Afficher une taille de fichier lisible pour l'utilisateur au lieu d'un nombre d'octets brut.

**Exemple dans une liste de médias** :
```php
// Dans une vue
foreach ($medias as $media) {
    echo "<li>{$media->title} - {$media->getFormattedFileSize()}</li>";
}

// Affichage :
// - Logo.png - 45.23 Ko
// - Video.mp4 - 12.5 Mo
// - Document.pdf - 1.2 Mo
```

</details>

---

### Étape 5.2 : Méthode de suppression sécurisée

```php
/**
 * Supprime le fichier physique ET l'entrée en base de données
 * 
 * @return bool Succès de la suppression
 */
public function delete(): bool
{
    $mediaModel = model('MediaModel');
    
    // Vérifier que l'ID existe
    if (empty($this->id)) {
        return false;
    }
    
    // Supprimer le fichier physique s'il existe
    if ($this->fileExists()) {
        unlink($this->getAbsolutePath());
    }
    
    // Supprimer l'entrée en base
    return $mediaModel->delete($this->id);
}
```

### Question 5.2 ⚠️
Cette méthode présente un **problème potentiel**. Si la suppression du fichier réussit mais que `$mediaModel->delete()` échoue, que se passe-t-il ?

<details>
<summary>🤔 Problème identifié</summary>

**Scénario problématique** :
1. Le fichier physique est supprimé avec `unlink()` ✅
2. La suppression en base de données échoue (erreur SQL, connexion perdue...) ❌

**Résultat** : Le fichier n'existe plus sur le serveur mais l'entrée reste en base → **incohérence des données**.

**La solution** : Utiliser une **transaction** dans le **MediaModel**, pas dans l'Entity !

</details>

---

### 📚 Comprendre les transactions et la séparation des responsabilités

#### Principe important : Entity vs Model

Avant de voir les transactions, rappelons les **responsabilités** :

| Composant | Responsabilité | Exemples |
|-----------|---------------|----------|
| **Entity** | Logique métier sur **une ligne** | `isImage()`, `getUrl()`, `fileExists()` |
| **Model** | Accès à la **base de données** | Requêtes SQL, transactions, validation |
| **Controller** | Orchestration | Appeler le model, gérer les réponses |

**Règle d'or** : Une Entity ne doit **jamais** se connecter directement à la base de données !

#### Qu'est-ce qu'une transaction ?

Une **transaction** est un ensemble d'opérations qui doivent **toutes réussir** ou **toutes échouer ensemble**. C'est le principe du "tout ou rien".

**Analogie bancaire** :
```
Transfert de 100€ du compte A vers le compte B :
1. Débiter 100€ du compte A
2. Créditer 100€ au compte B

Sans transaction :
- Si l'étape 1 réussit mais l'étape 2 échoue
- → Les 100€ disparaissent ! 💸

Avec transaction :
- Si l'étape 2 échoue
- → L'étape 1 est annulée (rollback)
- → Les comptes restent inchangés ✅
```

#### Les 4 propriétés ACID

Les transactions garantissent 4 propriétés (ACID) :

1. **Atomicité** : Tout ou rien (si une opération échoue, tout est annulé)
2. **Cohérence** : Les données restent valides (pas d'état incohérent)
3. **Isolation** : Les transactions ne s'interfèrent pas entre elles
4. **Durabilité** : Une fois validée, la transaction est permanente

---

### Étape 5.3 : Simplifier la méthode delete() de l'Entity

**Nouvelle version simplifiée** dans `Media.php` :

```php
/**
 * Supprime le média (délègue au MediaModel)
 * 
 * @return bool Succès de la suppression
 */
public function delete(): bool
{
    if (empty($this->id)) {
        return false;
    }
    
    $mediaModel = model('MediaModel');
    
    // Déléguer la suppression au Model qui gère la transaction
    return $mediaModel->deleteMedia($this->id);
}
```

### Question 5.3 🤔
Pourquoi cette nouvelle version est-elle plus simple et respecte-t-elle mieux l'architecture MVC ?

<details>
<summary>✅ Réponse</summary>

**Avantages** :
1. **Séparation des responsabilités** : L'Entity ne fait que déléguer au Model
2. **Simplicité** : Pas de logique de transaction dans l'Entity
3. **Réutilisabilité** : Le Model peut être appelé depuis n'importe où
4. **Testabilité** : Plus facile de mocker le Model dans les tests

**L'Entity se concentre sur** :
- Les getters/setters
- Les méthodes métier (calculs, formatage)
- La validation locale

**Le Model se concentre sur** :
- Les requêtes SQL
- Les transactions
- La cohérence des données

</details>

---

### Étape 5.4 : Améliorer le MediaModel avec une transaction

Maintenant, modifions la méthode `deleteMedia()` dans `MediaModel.php` :

```php
/**
 * Supprime un média (fichier + BDD) avec transaction
 * 
 * @param int $id ID du média à supprimer
 * @return bool Succès de la suppression
 */
public function deleteMedia($id): bool
{
    // Récupérer le média
    $media = $this->find($id);
    
    if (!$media) {
        return false;
    }
    
    // Démarrer une transaction
    $this->db->transStart();
    
    try {
        // 1. Supprimer l'entrée en BDD d'abord
        if (!$this->delete($id)) {
            throw new \Exception("Échec de la suppression en base de données");
        }
        
        // 2. Ensuite supprimer le fichier physique
        if ($media->fileExists()) {
            $filePath = $media->getAbsolutePath();
            
            if (!unlink($filePath)) {
                throw new \Exception("Échec de la suppression du fichier physique");
            }
        }
        
        // Finaliser la transaction
        $this->db->transComplete();
        
        // Vérifier le statut
        if ($this->db->transStatus() === false) {
            log_message('error', "Transaction échouée pour le média ID {$id}");
            return false;
        }
        
        return true;
        
    } catch (\Exception $e) {
        // Annuler la transaction en cas d'erreur
        $this->db->transRollback();
        log_message('error', 'Erreur suppression média : ' . $e->getMessage());
        return false;
    }
}
```

### Question 5.4 🔍
Pourquoi utilise-t-on `$this->db` dans le Model au lieu de `\Config\Database::connect()` ?

<details>
<summary>✅ Réponse</summary>

**Dans CodeIgniter 4**, le Model hérite de `CodeIgniter\Model` qui possède déjà une propriété `$db` :

```php
class MediaModel extends Model
{
    // $this->db est automatiquement disponible !
}
```

**Avantages** :
- Pas besoin de reconnecter à chaque fois
- Utilise la même connexion pour toutes les requêtes du Model
- Plus performant (réutilisation de la connexion)
- Plus cohérent avec l'architecture CodeIgniter

**Alternative** (à éviter dans un Model) :
```php
$db = \Config\Database::connect(); // ❌ Crée une nouvelle connexion
```

</details>

---

### Question 5.5 📊
Analysez le flux de la transaction. Que se passe-t-il dans chacun de ces cas ?

**Cas A** : La suppression BDD réussit, mais `unlink()` échoue
**Cas B** : La suppression BDD échoue
**Cas C** : Tout réussit

<details>
<summary>✅ Réponse détaillée</summary>

**Cas A : BDD OK, unlink échoue**
```
1. transStart() → Transaction démarre
2. delete($id) → ✅ Réussi (mais pas encore committé)
3. unlink() → ❌ Échoue
4. throw Exception
5. transRollback() → Annulation
6. Résultat : L'entrée BDD est RESTAURÉE, fichier toujours présent
```

**Cas B : BDD échoue**
```
1. transStart() → Transaction démarre
2. delete($id) → ❌ Échoue
3. throw Exception (on ne va même pas à unlink)
4. transRollback() → Annulation
5. Résultat : Rien n'a changé
```

**Cas C : Tout réussit**
```
1. transStart() → Transaction démarre
2. delete($id) → ✅ Réussi
3. unlink() → ✅ Réussi
4. transComplete() → Commit
5. Résultat : Média supprimé (BDD + fichier)
```

**Important** : Grâce à la transaction, on n'aura **jamais** une situation où le fichier est supprimé mais pas l'entrée BDD (ou l'inverse).

</details>

---

### Question 5.6 💭
Pourquoi supprime-t-on **d'abord** l'entrée en BDD puis le fichier, et pas l'inverse ?

<details>
<summary>🎯 Réponse</summary>

**Ordre recommandé : BDD → Fichier**

**Raisons** :
1. **Rollback possible** : Si on supprime le fichier d'abord et que la BDD échoue, on ne peut pas "annuler" la suppression du fichier physique
2. **Transactions ne gèrent que la BDD** : Les opérations fichier (unlink) ne font pas partie de la transaction SQL
3. **Impact utilisateur** : Une entrée BDD sans fichier peut afficher une image par défaut, un fichier sans entrée BDD reste orphelin à jamais

**Schéma de l'ordre** :
```
✅ BON ORDRE :
1. DELETE FROM media (dans transaction)
2. Si échec → rollback → STOP
3. unlink() fichier
4. Si échec → fichier reste mais on sait que l'entrée BDD n'existe plus

❌ MAUVAIS ORDRE :
1. unlink() fichier
2. Si tout OK → fichier supprimé
3. DELETE FROM media
4. Si échec → fichier disparu mais entrée BDD reste → INCOHÉRENCE !
```

**Cas exceptionnel** :
Pour des fichiers très volumineux (plusieurs Go), on peut préférer supprimer le fichier d'abord pour libérer l'espace disque, mais il faut alors :
- Logger l'opération
- Avoir un système de nettoyage des entrées BDD orphelines

</details>

---

## Partie 6 : Modification du MediaModel

### Étape 6.1 : Lier l'Entity au Model

Modifiez le `MediaModel.php` :

```php
protected $returnType = 'App\Entities\Media'; // Au lieu de 'array'
```

### Question 6.1 🔄
Maintenant que le modèle retourne des instances de `Media`, qu'est-ce qui change dans votre code contrôleur ?

<details>
<summary>✅ Avant / Après</summary>

**Avant (avec array)** :
```php
$media = $mediaModel->find(1);
echo base_url($media['file_path']);
$size = filesize(FCPATH . $media['file_path']);
```

**Après (avec Entity)** :
```php
$media = $mediaModel->find(1);
echo $media->getUrl();
$size = $media->getFormattedFileSize();

// Bonus : autocomplétion IDE !
$media->isImage(); // ✅ Suggéré automatiquement
```

</details>

---

### Étape 6.2 : Simplifier la méthode deleteMedia

**Ancienne version** (dans MediaModel.php) :
```php
public function deleteMedia($id) {
    $fichier = $this->find($id);
    if ($fichier) {
        $chemin = FCPATH . $fichier['file_path'];
        if (file_exists($chemin)) {
            unlink($chemin);
            return $this->delete($id);
        }
    }
    return false;
}
```

### Question 6.2 ✂️
Maintenant que vous avez une méthode `delete()` dans l'Entity Media, réécrivez `deleteMedia()` en utilisant cette nouvelle méthode.

<details>
<summary>✅ Version simplifiée</summary>

```php
public function deleteMedia($id): bool
{
    $media = $this->find($id);
    
    if (!$media) {
        return false;
    }
    
    // La logique est désormais dans l'Entity
    return $media->delete();
}
```

**Encore mieux** : On pourrait même supprimer cette méthode et appeler directement :
```php
// Dans le contrôleur
$media = $mediaModel->find($id);
$media?->delete();
```

</details>

---
## Partie 7 : Mise à jour du helper `utils` pour la compatibilité avec l’Entity Media

### Étape 7.1 : Comprendre le problème

Avant la refonte, `upload_file()` :
- insérait les données manuellement dans la table `media`,
- retournait parfois un **chemin de fichier** ou un **tableau d’erreur**.

Or, depuis la Partie 6 :
- le `MediaModel` renvoie une **Entity**,
- les méthodes comme `$media->getUrl()` ou `$media->delete()` dépendent de cette Entity.

> 🔧 Il faut donc faire en sorte que `upload_file()` :
> 1. Crée le fichier physique,
> 2. Crée ou mette à jour l’entrée `Media` via le `MediaModel`,
> 3. Retourne directement **une instance de `App\Entities\Media`**.

---

### Étape 7.2 : Nouvelle version de `upload_file()`

Ouvrez `app/Helpers/utils_helper.php` et modifiez la fonction existante :

```php
if (!function_exists('upload_file')) {
    /**
     * Upload d’un fichier média avec gestion de l’Entity Media
     *
     * @param \CodeIgniter\Files\File $file - Fichier à uploader
     * @param string $subfolder - Sous-dossier (ex: avatars, recipes)
     * @param string|null $customName - Nom personnalisé du fichier
     * @param array|null $mediaData - Données associées (entity_id, entity_type, title, alt)
     * @param bool $isMultiple - Si false, remplace l’ancien média lié
     * @param array $acceptedMimeTypes - Types MIME autorisés
     * @param int $maxSize - Taille max en Ko
     * @return \App\Entities\Media|array - L’Entity Media ou un tableau d’erreur
     */
    function upload_file(
        \CodeIgniter\Files\File $file,
        string $subfolder = '',
        string $customName = null,
        array $mediaData = null,
        bool $isMultiple = false,
        array $acceptedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
        int $maxSize = 2048
    ) {
        // 1️⃣ Vérification du fichier
        if ($file->getError() !== UPLOAD_ERR_OK) {
            return ['status' => 'error', 'message' => getUploadErrorMessage($file->getError())];
        }

        if ($file->hasMoved()) {
            return ['status' => 'error', 'message' => 'Le fichier a déjà été déplacé.'];
        }

        if (!in_array($file->getMimeType(), $acceptedMimeTypes)) {
            return ['status' => 'error', 'message' => 'Type de fichier non accepté.'];
        }

        if ($file->getSizeByUnit('kb') > $maxSize) {
            return ['status' => 'error', 'message' => 'Fichier trop volumineux.'];
        }

        // 2️⃣ Définir le dossier de destination
        $year  = date('Y');
        $month = date('m');
        $uploadPath = FCPATH . 'uploads/' . trim($subfolder, '/') . '/' . $year . '/' . $month;

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0775, true);
        }

        // 3️⃣ Générer un nom propre
        helper('text');
        $baseName = $customName ? url_title($customName, '-', true) : pathinfo($file->getClientName(), PATHINFO_FILENAME);
        $ext = $file->getExtension();
        $newName = $baseName . '-' . uniqid() . '.' . $ext;

        // 4️⃣ Déplacer le fichier
        $file->move($uploadPath, $newName);
        $relativePath = 'uploads/' . trim($subfolder, '/') . '/' . $year . '/' . $month . '/' . $newName;

        // 5️⃣ Enregistrer ou mettre à jour le média
        $mediaModel = model('MediaModel');

        if (!$isMultiple && isset($mediaData['entity_id'], $mediaData['entity_type'])) {
            $existing = $mediaModel
                ->where('entity_id', $mediaData['entity_id'])
                ->where('entity_type', $mediaData['entity_type'])
                ->first();

            if ($existing) {
                // Supprimer l’ancien fichier
                if ($existing->fileExists()) {
                    @unlink($existing->getAbsolutePath());
                }

                // Mettre à jour l’existant
                $mediaModel->update($existing->id, ['file_path' => $relativePath] + $mediaData);
                return $mediaModel->find($existing->id);
            }
        }

        // 6️⃣ Insertion d’un nouveau média
        $data = array_merge(['file_path' => $relativePath], $mediaData ?? []);
        $mediaId = $mediaModel->insert($data, true);

        return $mediaModel->find($mediaId);
    }
}
```

---

### Étape 7.3 : Points clés de cette version

| Étape | Action | Explication |
|:------|:--------|:------------|
| 1 | Vérification du fichier | Empêche les uploads invalides ou trop lourds |
| 2 | Création du dossier | Organise les fichiers par `année/mois` |
| 3 | Génération du nom | Évite les collisions avec `uniqid()` |
| 4 | Déplacement du fichier | Sauvegarde physique côté serveur |
| 5 | Vérification de doublon | Si `$isMultiple = false`, remplace l’ancien fichier |
| 6 | Création d’un nouvel enregistrement | Retourne directement une instance `Media` |

---

### Étape 7.4 : Exemple d’utilisation

Avec le code du contrôleur vu en **Partie 8.5**, vous pouvez désormais écrire :

```php
$result = upload_file(
    $avatarFile,
    'avatars',
    $user->username,
    [
        'entity_id' => $user->id,
        'entity_type' => 'user',
        'title' => 'Avatar de ' . $user->username,
        'alt' => 'Photo de profil'
    ]
);

if ($result instanceof \App\Entities\Media) {
    echo "✅ Upload réussi : " . $result->getUrl();
} else {
    echo "❌ Erreur : " . $result['message'];
}
```

---

### Question 7.1 🧠
Pourquoi retourne-t-on **une instance `Media`** plutôt qu’un simple tableau contenant `file_path` ?

<details>
<summary>✅ Réponse</summary>

Parce que :
- L’Entity offre des **méthodes utilitaires** (`getUrl()`, `delete()`, `fileExists()`).
- Elle garantit la **cohérence** avec le `MediaModel`.
- Les contrôleurs de la Partie 8 et 9 attendent **un objet Media**, pas un tableau.
</details>

---

### Question 7.2 ⚙️
Que se passe-t-il si `$isMultiple = false` et qu’un média existe déjà pour le même `entity_id` et `entity_type` ?

<details>
<summary>✅ Réponse</summary>

L’ancien média est :
1. Supprimé physiquement du serveur (`unlink()`),
2. Mis à jour en BDD avec le **nouveau chemin**,
3. Retourne la même Entity, mise à jour avec le nouveau fichier.
</details>

---

### Question 7.3 🚀
Pourquoi est-il utile que `upload_file()` crée automatiquement le dossier `uploads/[subfolder]/[année]/[mois]` ?

<details>
<summary>✅ Réponse</summary>

- **Organisation naturelle** : évite les dossiers saturés.
- **Traçabilité** : facilite les sauvegardes et nettoyages mensuels.
- **Compatibilité** : fonctionne pour tout type d’entité (user, recipe, etc.).
</details>

---

## Partie 8 : Application pratique - Ajouter un avatar à l'Entity User

Maintenant que vous maîtrisez l'Entity Media, appliquons ces connaissances pour gérer l'**avatar** d'un utilisateur.

### Contexte

Dans votre application, les utilisateurs peuvent avoir un avatar (photo de profil). Cette image est stockée dans la table `media` avec :
- `entity_type = 'user'`
- `entity_id = id de l'utilisateur`

### Question 8.1 🤔
D'après vous, quelle relation existe-t-il entre les tables `user` et `media` pour les avatars ?

<details>
<summary>✅ Réponse</summary>

**Relation : One-to-One (1:1)**

- Un utilisateur peut avoir **un seul avatar** (une image principale)
- Un avatar appartient à **un seul utilisateur**

**En SQL** :
```sql
SELECT * FROM media 
WHERE entity_type = 'user' 
AND entity_id = 5
LIMIT 1;
```

C'est différent des recettes qui peuvent avoir **plusieurs images** (relation 1:N).

</details>

---

### Étape 8.1 : Ajouter une méthode getAvatar() dans User.php

Ouvrez `app/Entities/User.php` et ajoutez cette méthode :

```php
/**
 * Récupère l'avatar de l'utilisateur
 * 
 * @return Media|null L'instance Media de l'avatar ou null
 */
public function getAvatar(): ?Media
{
    $mediaModel = model('MediaModel');
    
    $avatar = $mediaModel
        ->where('entity_type', 'user')
        ->where('entity_id', $this->id)
        ->first();
    
    return $avatar; // Retourne une instance de Media ou null
}
```

### Question 8.2 📝
Pourquoi le type de retour est `?Media` et pas `Media` ?

<details>
<summary>✅ Réponse</summary>

**Le `?` signifie "nullable"** : la méthode peut retourner :
- Une instance de `Media` si l'utilisateur a un avatar ✅
- `null` si l'utilisateur n'a pas d'avatar ⚠️

**Sans le `?`** : On promet de toujours retourner un objet Media, ce qui causerait une erreur si l'utilisateur n'a pas d'avatar.

**Utilisation** :
```php
$avatar = $user->getAvatar();

if ($avatar !== null) {
    echo $avatar->getUrl();
} else {
    echo "Pas d'avatar";
}
```

</details>

---

### Étape 8.2 : Méthode pour obtenir l'URL de l'avatar avec fallback

```php
/**
 * Retourne l'URL de l'avatar ou une image par défaut
 * 
 * @param string $default URL de l'image par défaut
 * @return string URL de l'avatar
 */
public function getAvatarUrl(string $default = 'assets/img/default-avatar.png'): string
{
    $avatar = $this->getAvatar();
    
    if ($avatar && $avatar->fileExists()) {
        return $avatar->getUrl();
    }
    
    return base_url($default);
}
```

### Question 8.3 🎨
Pourquoi vérifier `$avatar->fileExists()` en plus de tester si `$avatar` existe ?

<details>
<summary>✅ Réponse</summary>

**Deux niveaux de vérification** :

1. **`$avatar` existe** → Il y a une entrée en base de données
2. **`$avatar->fileExists()`** → Le fichier existe physiquement sur le serveur

**Cas problématiques** :
- Fichier supprimé manuellement du serveur
- Migration incomplète
- Corruption du système de fichiers

**Avec cette double vérification**, on évite d'afficher une image cassée (404) et on affiche plutôt l'avatar par défaut.

</details>

---

### Étape 8.3 : Méthode pour vérifier si l'utilisateur a un avatar

```php
/**
 * Vérifie si l'utilisateur a un avatar valide
 * 
 * @return bool
 */
public function hasAvatar(): bool
{
    $avatar = $this->getAvatar();
    return $avatar !== null && $avatar->fileExists();
}
```

---

### Étape 8.4 : Utilisation dans la vue

Modifiez le formulaire `form.php` pour afficher l'avatar :

```php
<!-- Avant les champs du formulaire, dans la card-body -->
<div class="row mb-3">
    <div class="col-12 text-center">
        <!-- Affichage de l'avatar -->
        <img src="<?= isset($user) ? $user->getAvatarUrl() : base_url('assets/img/default-avatar.png') ?>" 
             alt="Avatar" 
             class="rounded-circle mb-3" 
             style="width: 150px; height: 150px; object-fit: cover;">
        
        <?php if(isset($user) && $user->hasAvatar()): ?>
            <p class="text-muted small">Avatar actuel</p>
        <?php else: ?>
            <p class="text-muted small">Aucun avatar (image par défaut)</p>
        <?php endif; ?>
    </div>
</div>

<!-- Champ pour uploader un nouvel avatar -->
<div class="col-12 mb-3">
    <label for="avatar" class="form-label">
        Changer d'avatar
        <?php if(isset($user)): ?>
            <small class="text-muted">(Laisser vide pour conserver l'actuel)</small>
        <?php endif; ?>
    </label>
    <input type="file" 
           name="avatar" 
           id="avatar" 
           class="form-control" 
           accept="image/jpeg,image/png,image/gif,image/webp">
    <div class="form-text">
        Formats acceptés : JPG, PNG, GIF, WebP. Taille maximale : 2 Mo.
    </div>
</div>
```

### Question 8.4 🖼️
À quoi sert l'attribut `accept="image/jpeg,image/png,image/gif,image/webp"` dans l'input file ?

<details>
<summary>✅ Réponse</summary>

**Fonction** : Limite les types de fichiers sélectionnables dans l'explorateur de fichiers.

**Avantages** :
- Améliore l'expérience utilisateur (seules les images sont affichées)
- Première barrière de validation (côté client)

**Attention** : Ce n'est **pas suffisant** pour la sécurité ! Il faut **toujours valider côté serveur** car cette restriction peut être contournée.

**Dans le code** :
```php
// Validation serveur nécessaire
$file = $this->request->getFile('avatar');
if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
    // Rejeter le fichier
}
```

</details>

---

### Étape 8.5 : Traitement de l'upload dans le contrôleur

Modifiez la méthode `update()` dans `app/Controllers/Admin/User.php` :

```php
public function update()
{
    $userModel = model('UserModel');
    $data = $this->request->getPost();
    $id = $this->request->getPost('id');
    
    $user = $userModel->find($id);
    
    if (!$user) {
        $this->error('Utilisateur inexistant');
        return $this->redirect('/admin/user');
    }
    
    // Gestion du mot de passe
    if (empty($data['password'])) {
        unset($data['password']);
    }
    
    // Remplir les données
    $user->fill($data);
    
    // Gestion de l'avatar
    $avatarFile = $this->request->getFile('avatar');
    
    if ($avatarFile && $avatarFile->isValid() && !$avatarFile->hasMoved()) {
        // Utilisation du helper upload_file
        helper('utils');
        
        $result = upload_file(
            $avatarFile,
            'avatars',                    // Sous-dossier
            $user->username,              // Nom personnalisé
            [
                'entity_id' => $user->id,
                'entity_type' => 'user',
                'title' => 'Avatar de ' . $user->username,
                'alt' => 'Photo de profil'
            ],
            false,                        // Un seul avatar par utilisateur
            ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            2048                          // 2 Mo max
        );
        
        if (is_array($result) && $result['status'] === 'error') {
            $this->error($result['message']);
        } else {
            $this->success('Avatar mis à jour avec succès.');
        }
    }
    
    // Sauvegarde de l'utilisateur
    if ($userModel->save($user)) {
        $this->success('Utilisateur mis à jour avec succès.');
        return $this->redirect('/admin/user/' . $user->id);
    } else {
        $this->error('Erreur lors de la mise à jour.');
        return $this->redirect('/admin/user/' . $user->id);
    }
}
```

### Question 8.5 🔍
Observez le code ci-dessus. Que fait le paramètre `false` dans `upload_file()` ? Quelle est sa signification ?

<details>
<summary>✅ Réponse</summary>

**Le paramètre `$isMultiple`** :

```php
upload_file(
    $file,
    $subfolder,
    $customName,
    $mediaData,
    false,  // ← Ici : $isMultiple = false
    ...
)
```

**Signification** :
- `false` → **Un seul média** autorisé pour cette combinaison `entity_id` + `entity_type`
- `true` → **Plusieurs médias** autorisés (galerie d'images)

**Comportement avec `false`** (pour les avatars) :
```php
if (!$isMultiple) {
    // Si un ancien avatar existe, il sera remplacé
    $existingMedia = $mediaModel->where([
        'entity_id' => $user->id,
        'entity_type' => 'user'
    ])->first();
    
    if ($existingMedia) {
        // Mise à jour de l'avatar existant
        $mediaModel->update($existingMedia['id'], [...]);
    }
}
```

**Exemple avec `true`** (pour une galerie de recette) :
```php
upload_file($file, 'recipes', 'recette-1', [
    'entity_id' => 1,
    'entity_type' => 'recipe'
], true); // Permet d'ajouter plusieurs images
```

</details>

---

### Étape 8.6 : Méthode pour supprimer l'avatar

Ajoutez dans `User.php` :

```php
/**
 * Supprime l'avatar de l'utilisateur
 * 
 * @return bool Succès de la suppression
 */
public function deleteAvatar(): bool
{
    $avatar = $this->getAvatar();
    
    if ($avatar === null) {
        return false; // Pas d'avatar à supprimer
    }
    
    return $avatar->delete(); // Utilise la méthode de l'Entity Media
}
```

---

### Question 8.6 🧩
Créez un bouton dans le formulaire qui permet de supprimer l'avatar actuel (uniquement si l'utilisateur en a un).

<details>
<summary>✅ Solution</summary>

**Dans form.php**, après l'affichage de l'avatar :

```php
<?php if(isset($user) && $user->hasAvatar()): ?>
    <button type="button" 
            class="btn btn-danger btn-sm mt-2" 
            onclick="deleteAvatar(<?= $user->id ?>)">
        <i class="fas fa-trash"></i> Supprimer l'avatar
    </button>
<?php endif; ?>

<script>
function deleteAvatar(userId) {
    if (!confirm('Voulez-vous vraiment supprimer cet avatar ?')) {
        return;
    }
    
    fetch('<?= base_url('admin/user/delete-avatar') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ id_user: userId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Recharger la page
        } else {
            alert('Erreur : ' + data.message);
        }
    });
}
</script>
```

**Dans User.php (contrôleur)**, ajoutez :

```php
public function deleteAvatar()
{
    $id = $this->request->getPost('id_user');
    $userModel = model('UserModel');
    
    $user = $userModel->find($id);
    
    if (!$user) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Utilisateur introuvable'
        ]);
    }
    
    if ($user->deleteAvatar()) {
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Avatar supprimé avec succès'
        ]);
    } else {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erreur lors de la suppression'
        ]);
    }
}
```

**Route à ajouter** dans `Routes.php` :
```php
$routes->post('admin/user/delete-avatar', 'Admin\User::deleteAvatar', ['filter' => 'auth:administrateur']);
```

</details>

---

## Partie 9 : Code complet et test

### 9.1 Code final de l'Entity Media

<details>
<summary>📄 Voir Media.php complet et fonctionnel</summary>

```php
<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Media extends Entity
{
    protected $attributes = [
        'id'          => null,
        'file_path'   => null,
        'entity_id'   => null,
        'entity_type' => null,
        'title'       => null,
        'alt'         => null,
        'created_at'  => null,
        'updated_at'  => null,
        'deleted_at'  => null,
    ];

    protected $casts = [
        'id'          => 'integer',
        'entity_id'   => 'integer',
        'entity_type' => 'string',
        'file_path'   => 'string',
        'title'       => 'string',
        'alt'         => 'string',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Retourne l'URL complète du fichier média
     */
    public function getUrl(): string
    {
        return base_url($this->file_path);
    }

    /**
     * Retourne l'extension du fichier en minuscules
     */
    public function getFileExtension(): string
    {
        return strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));
    }

    /**
     * Vérifie si le média est une image
     */
    public function isImage(): bool
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        return in_array($this->getFileExtension(), $imageExtensions);
    }

    /**
     * Vérifie si le type d'entité est valide
     */
    public function isValidEntityType(): bool
    {
        $validTypes = ['user', 'recipe', 'recipe_mea', 'step', 'ingredient', 'brand'];
        return in_array($this->entity_type, $validTypes);
    }

    /**
     * Retourne le chemin absolu du fichier sur le serveur
     */
    public function getAbsolutePath(): string
    {
        return FCPATH . $this->file_path;
    }

    /**
     * Vérifie si le fichier existe physiquement
     */
    public function fileExists(): bool
    {
        return file_exists($this->getAbsolutePath());
    }

    /**
     * Retourne la taille du fichier en octets (ou false si inexistant)
     */
    public function getFileSize(): int|false
    {
        if (!$this->fileExists()) {
            return false;
        }
        return filesize($this->getAbsolutePath());
    }

    /**
     * Retourne la taille du fichier formatée (ex: "1.5 MB")
     */
    public function getFormattedFileSize(): string
    {
        $size = $this->getFileSize();
        
        if ($size === false) {
            return 'N/A';
        }
        
        $units = ['o', 'Ko', 'Mo', 'Go'];
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        
        return round($size / pow(1024, $power), 2) . ' ' . $units[$power];
    }

    /**
     * Supprime le média (délègue au MediaModel)
     * 
     * @return bool Succès de la suppression
     */
    public function delete(): bool
    {
        if (empty($this->id)) {
            return false;
        }
        
        $mediaModel = model('MediaModel');
        
        // Déléguer la suppression au Model qui gère la transaction
        return $mediaModel->deleteMedia($this->id);
    }
}
```

</details>

---

### 9.2 Code final du MediaModel (avec transaction)

<details>
<summary>📄 Voir MediaModel.php complet et mis à jour</summary>

```php
<?php

namespace App\Models;

use CodeIgniter\Model;

class MediaModel extends Model
{
    protected $table            = 'media';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'App\Entities\Media'; // ← Modifié pour utiliser l'Entity
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['file_path','entity_id', 'entity_type','title','alt'];
    
    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'file_path'   => 'required|string|is_unique[media.file_path,id,{id}]',
        'entity_id'   => 'required|integer',
        'entity_type' => 'required|in_list[user,recipe,recipe_mea,step,ingredient,brand]',
        'title'       => 'permit_empty|max_length[255]',
        'alt'         => 'permit_empty|max_length[255]',
    ];

    protected $validationMessages = [
        'file_path' => [
            'required'  => 'Le chemin du fichier est obligatoire.',
            'is_unique' => 'Ce fichier existe déjà.',
        ],
        'entity_id' => [
            'required' => 'L'ID de l'entité est obligatoire.',
            'integer'  => 'L'ID de l'entité doit être un nombre.',
        ],
        'entity_type' => [
            'required' => 'Le type d'entité est obligatoire.',
            'in_list'  => 'Le type d'entité doit être parmi : user, recipe, step, ingredient ou brand.',
        ],
        'title' => [
            'max_length' => 'Le titre ne peut pas dépasser 255 caractères.',
        ],
        'alt' => [
            'max_length' => 'Le texte alternatif ne peut pas dépasser 255 caractères.',
        ],
    ];

    /**
     * Supprime un média (fichier + BDD) avec transaction
     * 
     * @param int $id ID du média à supprimer
     * @return bool Succès de la suppression
     */
    public function deleteMedia($id): bool
    {
        // Récupérer le média
        $media = $this->find($id);
        
        if (!$media) {
            return false;
        }
        
        // Démarrer une transaction
        $this->db->transStart();
        
        try {
            // 1. Supprimer l'entrée en BDD d'abord
            if (!$this->delete($id)) {
                throw new \Exception("Échec de la suppression en base de données");
            }
            
            // 2. Ensuite supprimer le fichier physique
            if ($media->fileExists()) {
                $filePath = $media->getAbsolutePath();
                
                if (!unlink($filePath)) {
                    throw new \Exception("Échec de la suppression du fichier physique");
                }
            }
            
            // Finaliser la transaction
            $this->db->transComplete();
            
            // Vérifier le statut
            if ($this->db->transStatus() === false) {
                log_message('error', "Transaction échouée pour le média ID {$id}");
                return false;
            }
            
            return true;
            
        } catch (\Exception $e) {
            // Annuler la transaction en cas d'erreur
            $this->db->transRollback();
            log_message('error', 'Erreur suppression média : ' . $e->getMessage());
            return false;
        }
    }
}
```

</details>

---

### 9.3 Code complet de l'Entity User (avec méthodes avatar)

<details>
<summary>📄 Voir User.php avec les méthodes avatar ajoutées</summary>

```php
<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;
use App\Entities\Media;

class User extends Entity
{
    protected $attributes = [
        'id'            => null,
        'email'         => null,
        'password'      => null,
        'username'      => null,
        'first_name'    => null,
        'last_name'     => null,
        'birthdate'     => null,
        'id_permission' => 2,
        'created_at'    => null,
        'updated_at'    => null,
        'deleted_at'    => null,
    ];
    
    protected $casts = [
        'id'            => 'integer',
        'email'         => 'string',
        'password'      => 'string',
        'username'      => 'string',
        'first_name'    => 'string',
        'last_name'     => 'string',
        'birthdate'     => 'datetime',
        'id_permission' => 'integer',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    protected $hidden = ['password'];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at', 'birthdate'];

    public function getFullName(): string
    {
        return trim($this->attributes['first_name'] . ' ' . $this->attributes['last_name']);
    }

    public function isActive(): bool
    {
        return $this->attributes['deleted_at'] === null;
    }

    public function setPassword(string $password)
    {
        $this->attributes['password'] = password_hash($password, PASSWORD_DEFAULT);
        return $this;
    }
    
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->attributes['password']);
    }

    public function isAdmin(): bool
    {
        return $this->check('administrateur');
    }

    public function check(string $slug): bool
    {
        $userPermissionSlug = $this->getPermissionSlug();

        if ($userPermissionSlug === $slug) {
            return true;
        }
        return false;
    }

    public function getPermissionSlug(): string
    {
        $upm = model('UserPermissionModel');
        $permission = $upm->find($this->attributes['id_permission']);

        return $permission ? $permission['slug'] : 'utilisateur';
    }

    public function getPermissionName(): string
    {
        $upm = model('UserPermissionModel');
        $permission = $upm->find($this->attributes['id_permission']);

        return $permission ? $permission['name'] : 'Utilisateur';
    }

    /**
     * Récupère l'avatar de l'utilisateur
     * 
     * @return Media|null L'instance Media de l'avatar ou null
     */
    public function getAvatar(): ?Media
    {
        $mediaModel = model('MediaModel');
        
        $avatar = $mediaModel
            ->where('entity_type', 'user')
            ->where('entity_id', $this->id)
            ->first();
        
        return $avatar;
    }

    /**
     * Retourne l'URL de l'avatar ou une image par défaut
     * 
     * @param string $default URL de l'image par défaut
     * @return string URL de l'avatar
     */
    public function getAvatarUrl(string $default = 'assets/img/default-avatar.png'): string
    {
        $avatar = $this->getAvatar();
        
        if ($avatar && $avatar->fileExists()) {
            return $avatar->getUrl();
        }
        
        return base_url($default);
    }

    /**
     * Vérifie si l'utilisateur a un avatar valide
     * 
     * @return bool
     */
    public function hasAvatar(): bool
    {
        $avatar = $this->getAvatar();
        return $avatar !== null && $avatar->fileExists();
    }

    /**
     * Supprime l'avatar de l'utilisateur
     * 
     * @return bool Succès de la suppression
     */
    public function deleteAvatar(): bool
    {
        $avatar = $this->getAvatar();
        
        if ($avatar === null) {
            return false;
        }
        
        return $avatar->delete();
    }
}
```

</details>

---

### 9.4 Contrôleur User.php mis à jour (avec gestion avatar)

<details>
<summary>📄 Voir Admin/User.php avec upload et suppression d'avatar</summary>

```php
<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class User extends BaseController
{
    public function index()
    {
        return $this->view('/admin/user/index');
    }

    public function edit($id_user) 
    {
        $um = Model('UserModel');
        $user = $um->find($id_user);
        
        if (!$user) {
            $this->error('Utilisateur inexistant');
            return $this->redirect('/admin/user');
        }
        
        helper('form');
        $permissions = Model('UserPermissionModel')->findAll();
        
        return $this->view('/admin/user/form', [
            'user' => $user, 
            'permissions' => $permissions
        ]);
    }

    public function create() 
    {
        helper('form');
        $permissions = Model('UserPermissionModel')->findAll();

        return $this->view('/admin/user/form', ['permissions' => $permissions]);
    }

    public function update()
    {
        $userModel = model('UserModel');
        $data = $this->request->getPost();
        $id = $this->request->getPost('id');

        $user = $userModel->find($id);

        if (!$user) {
            $this->error('Utilisateur inexistant');
            return $this->redirect('/admin/user');
        }

        // Gestion du mot de passe
        if (empty($data['password'])) {
            unset($data['password']);
        }

        // Remplir l'utilisateur avec les nouvelles données
        $user->fill($data);

        // Gestion de l'avatar
        $avatarFile = $this->request->getFile('avatar');
        
        if ($avatarFile && $avatarFile->isValid() && !$avatarFile->hasMoved()) {
            helper('utils');
            
            $result = upload_file(
                $avatarFile,
                'avatars',
                $user->username,
                [
                    'entity_id' => $user->id,
                    'entity_type' => 'user',
                    'title' => 'Avatar de ' . $user->username,
                    'alt' => 'Photo de profil de ' . $user->username
                ],
                false, // Un seul avatar par utilisateur
                ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
                2048 // 2 Mo max
            );
            
            if (is_array($result) && $result['status'] === 'error') {
                $this->error($result['message']);
            } else {
                $this->success('Avatar mis à jour avec succès.');
            }
        }

        // Sauvegarde de l'utilisateur
        if ($userModel->save($user)) {
            $this->success('Utilisateur mis à jour avec succès.');
            return $this->redirect('/admin/user/' . $user->id);
        } else {
            $errors = $userModel->errors();
            foreach ($errors as $error) {
                $this->error($error);
            }
            return $this->redirect('/admin/user/' . $user->id);
        }
    }

    public function insert() 
    {
        $userModel = model('UserModel');
        $data = $this->request->getPost();

        if (empty($data['password'])) {
            $this->error('Le mot de passe est obligatoire.');
            return $this->redirect('/admin/user/new');
        }

        $user = new \App\Entities\User();
        $user->fill($data);

        if ($userModel->save($user)) {
            $this->success('Utilisateur créé avec succès.');
            
            // Gestion de l'avatar pour le nouvel utilisateur
            $avatarFile = $this->request->getFile('avatar');
            
            if ($avatarFile && $avatarFile->isValid() && !$avatarFile->hasMoved()) {
                helper('utils');
                
                $result = upload_file(
                    $avatarFile,
                    'avatars',
                    $user->username,
                    [
                        'entity_id' => $user->id,
                        'entity_type' => 'user',
                        'title' => 'Avatar de ' . $user->username,
                        'alt' => 'Photo de profil de ' . $user->username
                    ],
                    false,
                    ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
                    2048
                );
                
                if (is_array($result) && isset($result['status']) && $result['status'] === 'error') {
                    $this->warning('Utilisateur créé mais erreur avatar : ' . $result['message']);
                }
            }
            
            return $this->redirect('/admin/user/');
        } else {
            $errors = $userModel->errors();
            foreach ($errors as $error) {
                $this->error($error);
            }
            return $this->redirect('/admin/user/new');
        }
    }

    public function switchActive() 
    {
        $id = $this->request->getPost('id_user');
        $userModel = model('UserModel');

        $user = $userModel->withDeleted()->find($id);

        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Utilisateur introuvable'
            ]);
        }

        if ($user->isActive()) {
            $userModel->delete($id);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Utilisateur désactivé',
                'status' => 'inactive'
            ]);
        } else {
            if ($userModel->reactive($id)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Utilisateur activé',
                    'status' => 'active'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erreur lors de l\'activation'
                ]);
            }
        }
    }

    /**
     * Supprime l'avatar d'un utilisateur (AJAX)
     */
    public function deleteAvatar()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Requête non autorisée'
            ]);
        }
        
        $id = $this->request->getPost('id_user');
        $userModel = model('UserModel');
        
        $user = $userModel->find($id);
        
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Utilisateur introuvable'
            ]);
        }
        
        if ($user->deleteAvatar()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Avatar supprimé avec succès'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Aucun avatar à supprimer ou erreur lors de la suppression'
            ]);
        }
    }

    public function search()
    {
        $request = $this->request;

        if (!$request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Requête non autorisée']);
        }

        $um = Model('UserModel');

        $search = $request->getGet('search') ?? '';
        $page = (int)($request->getGet('page') ?? 1);
        $limit = 20;

        $result = $um->quickSearchForSelect2($search, $page, $limit);

        return $this->response->setJSON($result);
    }
}
```

</details>

---

### 9.5 Exercice final : Créer un contrôleur de test

Créez `app/Controllers/TestMedia.php` :

```php
<?php

namespace App\Controllers;

class TestMedia extends BaseController
{
    public function index()
    {
        $mediaModel = model('MediaModel');
        $media = $mediaModel->first();
        
        if (!$media) {
            echo "<h1>Aucun média en base de données</h1>";
            echo "<p>Veuillez d'abord uploader un fichier via l'interface.</p>";
            return;
        }
        
        echo "<h1>Test de l'Entity Media</h1>";
        echo "<div style='font-family: Arial; line-height: 1.8;'>";
        echo "<p><strong>ID :</strong> " . $media->id . "</p>";
        echo "<p><strong>Chemin :</strong> " . esc($media->file_path) . "</p>";
        echo "<p><strong>URL :</strong> <a href='" . $media->getUrl() . "' target='_blank'>" . $media->getUrl() . "</a></p>";
        echo "<p><strong>Extension :</strong> " . $media->getFileExtension() . "</p>";
        echo "<p><strong>Est une image :</strong> " . ($media->isImage() ? '✅ Oui' : '❌ Non') . "</p>";
        echo "<p><strong>Taille :</strong> " . $media->getFormattedFileSize() . "</p>";
        echo "<p><strong>Fichier existe :</strong> " . ($media->fileExists() ? '✅ Oui' : '❌ Non') . "</p>";
        echo "<p><strong>Type d'entité :</strong> " . esc($media->entity_type) . "</p>";
        echo "<p><strong>ID d'entité :</strong> " . $media->entity_id . "</p>";
        echo "<p><strong>Type valide :</strong> " . ($media->isValidEntityType() ? '✅ Oui' : '❌ Non') . "</p>";
        echo "<p><strong>Créé le :</strong> " . $media->created_at->toDateTimeString() . " (" . $media->created_at->humanize() . ")</p>";
        
        if ($media->isImage() && $media->fileExists()) {
            echo "<hr>";
            echo "<h2>Aperçu de l'image</h2>";
            echo "<img src='" . $media->getUrl() . "' alt='Test' style='max-width: 500px; border: 2px solid #ccc; border-radius: 8px;'>";
        }
        
        echo "</div>";
    }
    
    public function testUser($id = null)
    {
        if ($id === null) {
            echo "<h1>Test Avatar Utilisateur</h1>";
            echo "<p>Utilisez : /test-media/user/[ID]</p>";
            return;
        }
        
        $userModel = model('UserModel');
        $user = $userModel->find($id);
        
        if (!$user) {
            echo "<h1>Utilisateur introuvable</h1>";
            return;
        }
        
        echo "<h1>Test Avatar - " . esc($user->username) . "</h1>";
        echo "<div style='font-family: Arial; line-height: 1.8;'>";
        echo "<p><strong>Nom complet :</strong> " . esc($user->getFullName()) . "</p>";
        echo "<p><strong>Email :</strong> " . esc($user->email) . "</p>";
        echo "<p><strong>A un avatar :</strong> " . ($user->hasAvatar() ? '✅ Oui' : '❌ Non') . "</p>";
        echo "<p><strong>URL de l'avatar :</strong> <a href='" . $user->getAvatarUrl() . "' target='_blank'>" . $user->getAvatarUrl() . "</a></p>";
        
        echo "<hr>";
        echo "<h2>Aperçu</h2>";
        echo "<img src='" . $user->getAvatarUrl() . "' alt='Avatar' style='width: 150px; height: 150px; border-radius: 50%; border: 3px solid #007bff;'>";
        
        if ($user->hasAvatar()) {
            $avatar = $user->getAvatar();
            echo "<hr>";
            echo "<h2>Détails du fichier avatar</h2>";
            echo "<p><strong>Taille :</strong> " . $avatar->getFormattedFileSize() . "</p>";
            echo "<p><strong>Extension :</strong> " . $avatar->getFileExtension() . "</p>";
            echo "<p><strong>Chemin :</strong> " . esc($avatar->file_path) . "</p>";
        }
        
        echo "</div>";
    }
}
```

### Question finale 🎓
Ajoutez les routes correspondantes dans `app/

---

## 🎯 Récapitulatif des compétences acquises

✅ Création d'une Entity CodeIgniter 4  
✅ Configuration des attributs et casting de types  
✅ Implémentation de méthodes métier  
✅ Gestion des fichiers via une Entity  
✅ **Compréhension des transactions (ACID)**  
✅ **Gestion sécurisée de la suppression avec transaction**  
✅ Simplification du code avec l'OOP  
✅ Liaison Entity ↔ Model  
✅ **Application pratique : Avatar utilisateur**  
✅ **Relation entre entities (User ↔ Media)**

---

## 🚀 Pour aller plus loin

**Exercices bonus** :
1. Ajoutez une méthode `getThumbnail()` qui génère une miniature de 150x150px
2. Créez une méthode `moveTo($newPath)` pour déplacer un fichier
3. Implémentez `getAltOrTitle()` qui retourne `alt` ou `title` en fallback
4. Ajoutez une validation dans un setter personnalisé pour `entity_type`
5. **Créez une méthode `updateAvatar()` dans User.php qui gère upload + suppression de l'ancien**
6. **Ajoutez un système de redimensionnement automatique pour les avatars (max 300x300px)**

---

## 📝 Checklist de validation

Avant de considérer le TP terminé, vérifiez que :

- [ ] L'Entity Media est créée dans `app/Entities/Media.php`
- [ ] Tous les attributs sont déclarés avec leur type
- [ ] Au moins 5 méthodes métier sont implémentées
- [ ] La méthode `delete()` utilise une transaction
- [ ] Le `MediaModel` est modifié pour utiliser l'Entity
- [ ] Le contrôleur de test fonctionne correctement
- [ ] Vous comprenez la différence entre Entity et Model
- [ ] **Les méthodes avatar sont ajoutées à User.php**
- [ ] **L'upload d'avatar fonctionne dans le formulaire utilisateur**
- [ ] **Vous comprenez le principe des transactions ACID**

---

**Bon courage ! 💪**