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

** 💪Bon courage ! 💪**

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
Cette méthode présente un **problème potentiel**. Si la suppression du fichier réussit mais que `$mediaModel->delete()` échoue, que se passe-t-il ? Comment pourriez-vous améliorer ce code ?

<details>
<summary>🤔 Problème identifié</summary>

**Scénario problématique** :
1. Le fichier physique est supprimé avec `unlink()` ✅
2. La suppression en base de données échoue (erreur SQL, connexion perdue...) ❌

**Résultat** : Le fichier n'existe plus sur le serveur mais l'entrée reste en base → **incohérence des données**.

</details>

---

### 📚 Comprendre les transactions

Avant d'améliorer notre code, comprenons ce qu'est une **transaction**.

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

#### Syntaxe dans CodeIgniter 4

```php
$db = \Config\Database::connect();

// Démarrer une transaction
$db->transStart();

// Opérations de la base de données
$db->query("DELETE FROM media WHERE id = ?", [$id]);
$db->query("UPDATE recipe SET image = NULL WHERE id = ?", [$recipeId]);

// Finaliser la transaction
$db->transComplete();

// Vérifier le statut
if ($db->transStatus() === false) {
    // Au moins une opération a échoué → tout a été annulé
    echo "Erreur : transaction annulée";
} else {
    // Tout s'est bien passé
    echo "Transaction réussie";
}
```

### Question 5.2 bis 🛠️
Maintenant que vous comprenez les transactions, réécrivez la méthode `delete()` en utilisant une transaction pour garantir la cohérence.

<details>
<summary>✅ Solution avec transaction</summary>

```php
/**
 * Supprime le fichier physique ET l'entrée en base de données de manière atomique
 * 
 * @return bool Succès de la suppression
 */
public function delete(): bool
{
    // Connexion à la base de données
    $db = \Config\Database::connect();
    
    // Vérifier que l'ID existe
    if (empty($this->id)) {
        return false;
    }
    
    // Démarrer une transaction
    $db->transStart();
    
    try {
        $mediaModel = model('MediaModel');
        
        // 1. Supprimer l'entrée en BDD d'abord
        if (!$mediaModel->delete($this->id)) {
            throw new \Exception("Échec de la suppression en base de données");
        }
        
        // 2. Ensuite supprimer le fichier physique
        if ($this->fileExists()) {
            if (!unlink($this->getAbsolutePath())) {
                throw new \Exception("Échec de la suppression du fichier physique");
            }
        }
        
        // Finaliser la transaction
        $db->transComplete();
        
        // Vérifier le statut de la transaction
        if ($db->transStatus() === false) {
            log_message('error', "Transaction échouée pour le média ID {$this->id}");
            return false;
        }
        
        return true;
        
    } catch (\Exception $e) {
        // En cas d'erreur, annuler la transaction
        $db->transRollback();
        log_message('error', 'Erreur suppression média : ' . $e->getMessage());
        return false;
    }
}
```

**Explication du flux** :
1. Si la suppression BDD échoue → Exception levée → Rollback automatique
2. Si la suppression fichier échoue → Exception levée → Rollback (l'entrée BDD est restaurée)
3. Si tout réussit → Commit automatique à `transComplete()`

**Important** : Dans CodeIgniter 4, `transStart()` / `transComplete()` gère automatiquement le commit/rollback en fonction des erreurs détectées. Le `try/catch` permet une gestion plus fine.

</details>

---

### Question 5.3 💭
Pourquoi supprime-t-on **d'abord** l'entrée en BDD puis le fichier, et pas l'inverse ?

<details>
<summary>🎯 Réponse</summary>

**Ordre recommandé : BDD → Fichier**

**Raisons** :
1. **Rollback possible** : Si on supprime le fichier d'abord et que la BDD échoue, on ne peut pas "annuler" la suppression du fichier
2. **Impact utilisateur** : Une entrée BDD orpheline (sans fichier) est moins grave qu'un fichier orphelin (sans entrée BDD)
3. **Nettoyage** : Il est plus facile de faire un script de nettoyage pour supprimer les fichiers orphelins que l'inverse

**Cas exceptionnel** :
Si le fichier est très volumineux (plusieurs Go), on peut préférer le supprimer en premier pour libérer l'espace disque immédiatement, mais c'est rare.

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

## Partie 7 : Application pratique - Ajouter un avatar à l'Entity User

Maintenant que vous maîtrisez l'Entity Media, appliquons ces connaissances pour gérer l'**avatar** d'un utilisateur.

### Contexte

Dans votre application, les utilisateurs peuvent avoir un avatar (photo de profil). Cette image est stockée dans la table `media` avec :
- `entity_type = 'user'`
- `entity_id = id de l'utilisateur`

### Question 7.1 🤔
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

### Étape 7.1 : Ajouter une méthode getAvatar() dans User.php

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

### Question 7.2 📝
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

### Étape 7.2 : Méthode pour obtenir l'URL de l'avatar avec fallback

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

### Question 7.3 🎨
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

### Étape 7.3 : Méthode pour vérifier si l'utilisateur a un avatar

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

### Étape 7.4 : Utilisation dans la vue

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

### Question 7.4 🖼️
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

### Étape 7.5 : Traitement de l'upload dans le contrôleur

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

### Question 7.5 🔍
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

### Étape 7.6 : Méthode pour supprimer l'avatar

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

### Question 7.6 🧩
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

## Partie 8 : Code complet et test

### Code final de l'Entity

<details>
<summary>📄 Voir Media.php complet</summary>

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

    public function getUrl(): string
    {
        return base_url($this->file_path);
    }

    public function getFileExtension(): string
    {
        return strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));
    }

    public function isImage(): bool
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        return in_array($this->getFileExtension(), $imageExtensions);
    }

    public function isValidEntityType(): bool
    {
        $validTypes = ['user', 'recipe', 'recipe_mea', 'step', 'ingredient', 'brand'];
        return in_array($this->entity_type, $validTypes);
    }

    public function getAbsolutePath(): string
    {
        return FCPATH . $this->file_path;
    }

    public function fileExists(): bool
    {
        return file_exists($this->getAbsolutePath());
    }

    public function getFileSize(): int|false
    {
        if (!$this->fileExists()) {
            return false;
        }
        return filesize($this->getAbsolutePath());
    }

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

    public function delete(): bool
    {
        $mediaModel = model('MediaModel');
        
        if (empty($this->id)) {
            return false;
        }
        
        if ($this->fileExists()) {
            unlink($this->getAbsolutePath());
        }
        
        return $mediaModel->delete($this->id);
    }
}
```

</details>

---

### Exercice final : Créer un contrôleur de test

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
            echo "Aucun média en base de données";
            return;
        }
        
        echo "<h1>Test de l'Entity Media</h1>";
        echo "<p><strong>URL :</strong> " . $media->getUrl() . "</p>";
        echo "<p><strong>Extension :</strong> " . $media->getFileExtension() . "</p>";
        echo "<p><strong>Est une image :</strong> " . ($media->isImage() ? 'Oui' : 'Non') . "</p>";
        echo "<p><strong>Taille :</strong> " . $media->getFormattedFileSize() . "</p>";
        echo "<p><strong>Fichier existe :</strong> " . ($media->fileExists() ? 'Oui' : 'Non') . "</p>";
        echo "<p><strong>Type valide :</strong> " . ($media->isValidEntityType() ? 'Oui' : 'Non') . "</p>";
        echo "<p><strong>Créé le :</strong> " . $media->created_at->humanize() . "</p>";
    }
}
```

### Question finale 🎓
Ajoutez la route correspondante dans `app/Config/Routes.php` et testez votre Entity !

<details>
<summary>✅ Route à ajouter</summary>

```php
$routes->get('test-media', 'TestMedia::index');
```

Puis accédez à : `http://votre-site/test-media`

</details>

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