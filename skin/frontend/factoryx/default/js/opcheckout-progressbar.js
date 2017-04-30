Checkout.prototype.parent_resetPreviousSteps = Checkout.prototype.resetPreviousSteps;
Checkout.prototype.resetPreviousSteps = function () {
    this.parent_resetPreviousSteps();

    var progressBar = $('progressbar');
    var realSteps = [];

    for (i = 0; i < this.steps.size(); i++) {
        if ($('opc-' + this.steps[i])) {
            realSteps.push(this.steps[i]);
        }
    }

    if (progressBar) {
        var size = realSteps.size();
        size--;
        // 5 steps counted as the review step is not taken in the progress
        var progress = realSteps.indexOf(this.currentStep) / size * 100;
        progressBar.setStyle({width: progress + "%"});
        progressBar.setAttribute('aria-valuenow', progress);
        progressBar.update(progress + "%");
    }
    else {
        var realStepIndex = realSteps.indexOf(this.currentStep);

        // Remove the login step
        realStepIndex--;

        for (i = 0; i < realStepIndex; i++) {
            var progressCheckox = $('progresscheckbox-' + i);
            if (progressCheckox) {
                progressCheckox.setAttribute('checked', true);
            }
        }

        for (i; i < realSteps.size; i++) {
            var progressCheckox = $('progresscheckbox-' + i);
            if (progressCheckox) {
                progressCheckox.removeAttribute('checked');
            }
        }
    }
};
