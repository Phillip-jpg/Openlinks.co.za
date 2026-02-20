const tour = new Shepherd.Tour({
    defaults: {
        classes: 'shepherd-theme-arrows',
        zIndex: 99,
        cancelIcon: true
    }
});

tour.addStep('step', {
    text: 'Welcome to <b>Openlinks</b>, click next for a quick tour.',
    attachTo: '.tour-1 right',
    buttons: [
        {
            text: 'next',
            action: tour.next
        }
    ]
});

tour.addStep('step2', {
    text: 'This is where you will start, fill in all of these registration forms to get started',
    attachTo: '.tour-2 right',
    buttons: [
        {
            text: 'back',
            action: tour.back,
            classes: 'shepherd-button-secondary'
        },
        {
            text: 'next',
            action: tour.next
        }
    ]
});

tour.addStep('step3', {
text: 'This is myBBBEE, where you do myBBBEE stuff',
attachTo: '.tour-3 right',
buttons: [
    {
        text: 'back',
        action: tour.back,
        classes: 'shepherd-button-secondary'
    },
    {
        text: 'next',
        action: tour.next
    }
]
});


tour.addStep('step4', {
text: 'This is search, where you do search stuff',
attachTo: '.tour-4 right',
buttons: [
    {
        text: 'back',
        action: tour.back,
        classes: 'shepherd-button-secondary'
    },
    {
        text: 'next',
        action: tour.next
    }
]
});

tour.addStep('step5', {
text: 'This is notifications, where you do notification stuff',
attachTo: '.tour-5 right',
buttons: [
    {
        text: 'back',
        action: tour.back,
        classes: 'shepherd-button-secondary'
    },
    {
        text: 'next',
        action: tour.next
    }
]
});

tour.addStep('step6', {
text: 'This is messages, where you do message stuff',
attachTo: '.tour-6 right',
buttons: [
    {
        text: 'back',
        action: tour.back,
        classes: 'shepherd-button-secondary'
    },
    {
        text: 'next',
        action: tour.next
    }
]
});


tour.addStep('step7', {
text: 'You\'re all done!',
attachTo: '.tour-1 right',
buttons: [
    { 
    text: 'Done',
    action: tour.next
    }
]
});


tour.start()
