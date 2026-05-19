<?php loadPartial('head'); ?>
<?php loadPartial('navbar'); ?>

<?php if (!isset($listing) || !$listing): ?>
    <div class="error-container" style="text-align: center; padding: 50px;">
        <h2>Listing Not Found</h2>
        <p>The job listing you're trying to edit doesn't exist.</p>
        <a href="<?= url('listings') ?>" class="btn btn-primary">Back to Listings</a>
    </div>
<?php else: ?>

<section class="create-page">
  <div class="create-wrap">
    <div class="form-shell">
      <div class="form-hero">
        <span class="form-badge">Employer Portal</span>
        <h1>Edit Job Listing: <?= htmlspecialchars($listing->title ?? '') ?></h1>
      </div>

      <form method="POST" action="<?= url('listings/' . $listing->id) ?>" class="job-form">
        <input type="hidden" name="_method" value="PUT">
        
        <div class="form-section">
          <h2>Job Information</h2>
          
          <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
              <ul class="error-list">
                <?php foreach ($errors as $error): ?>
                  <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>
          
          <div class="form-grid">
            <div class="form-group full">
              <label for="title">Job Title <span class="required">*</span></label>
              <input type="text" id="title" name="title" placeholder="Frontend Developer" class="form-input" value="<?= htmlspecialchars($listing->title ?? '') ?>" />
            </div>

            <div class="form-group full">
              <label for="description">Job Description <span class="required">*</span></label>
              <textarea id="description" name="description" rows="5" placeholder="Describe the role, responsibilities, and expectations..." class="form-input"><?= htmlspecialchars($listing->description ?? '') ?></textarea>
            </div>

            <div class="form-group">
              <label for="salary">Annual Salary <span class="required">*</span></label>
              <input type="text" id="salary" name="salary" placeholder="₱500,000" class="form-input" value="<?= htmlspecialchars($listing->salary ?? '') ?>" />
            </div>

            <div class="form-group">
              <label for="requirements">Requirements</label>
              <input type="text" id="requirements" name="requirements" placeholder="React, Tailwind, PHP" class="form-input" value="<?= htmlspecialchars($listing->requirements ?? '') ?>" />
            </div>

            <div class="form-group full">
              <label for="benefits">Benefits</label>
              <input type="text" id="benefits" name="benefits" placeholder="Health insurance, remote work, bonuses" class="form-input" value="<?= htmlspecialchars($listing->benefits ?? '') ?>" />
            </div>

            <div class="form-group full">
              <label for="tags">Tags</label>
              <input type="text" id="tags" name="tags" placeholder="PHP, Laravel, Vue.js" class="form-input" value="<?= htmlspecialchars($listing->tags ?? '') ?>" />
              <small class="form-hint">Separate tags with commas</small>
            </div>
          </div>
        </div>

        <div class="form-section">
          <h2>Company Information & Location</h2>

          <div class="form-grid">
            <div class="form-group full">
              <label for="company">Company Name</label>
              <input type="text" id="company" name="company" placeholder="Prosple Inc." class="form-input" value="<?= htmlspecialchars($listing->company ?? '') ?>" />
            </div>

            <div class="form-group full">
              <label for="address">Address</label>
              <input type="text" id="address" name="address" placeholder="123 Business Ave" class="form-input" value="<?= htmlspecialchars($listing->address ?? '') ?>" />
            </div>

            <div class="form-group">
              <label for="city">City <span class="required">*</span></label>
              <input type="text" id="city" name="city" placeholder="Manila" class="form-input" value="<?= htmlspecialchars($listing->city ?? '') ?>" />
            </div>

            <div class="form-group">
              <label for="state">State / Province <span class="required">*</span></label>
              <input type="text" id="state" name="state" placeholder="Metro Manila" class="form-input" value="<?= htmlspecialchars($listing->state ?? '') ?>" />
            </div>

            <div class="form-group">
              <label for="phone">Phone</label>
              <input type="text" id="phone" name="phone" placeholder="+63 912 345 6789" class="form-input" value="<?= htmlspecialchars($listing->phone ?? '') ?>" />
            </div>

            <div class="form-group">
              <label for="email">Application Email <span class="required">*</span></label>
              <input type="email" id="email" name="email" placeholder="jobs@company.com" class="form-input" value="<?= htmlspecialchars($listing->email ?? '') ?>" />
            </div>
          </div>
        </div>

        <div class="action-row">
          <button type="submit" class="btn btn-primary">
            <i class="fa fa-floppy-disk"></i>
            Save Changes
          </button>

          <a href="<?= url('listings/' . $listing->id) ?>" class="btn btn-secondary">
            <i class="fa fa-xmark"></i>
            Cancel
          </a>
        </div>
      </form>
    </div>
  </div>
</section>

<?php endif; ?>

<?php loadPartial('footer'); ?>