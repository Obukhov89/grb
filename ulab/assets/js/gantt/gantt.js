'use strict';
const timelines = [];
const timelineLeftSides = [];
const timelineRightSides = [];
const table = document.querySelector('.scrollable-table');
let isResizing = false;
let isResizingByRightSide = false;
let isResizingByLeftSide = false;

let currentGrabTimelineId = null;

for (let item of $('.timeline')) {
    let itemNumber = $(item)[0].id.split("_")[1];
    const timeline = document.getElementById('timeline_' + itemNumber);
    const timelineRightSide = document.getElementById('timeline-right-side_' + itemNumber);
    const timelineLeftSide = document.getElementById('timeline-left-side_' + itemNumber);

    if (!timeline || !timelineLeftSide || !timelineRightSide) {
        continue;
    }

    timelines.push(timeline);
    timelineLeftSides.push(timelineLeftSide);
    timelineRightSides.push(timelineRightSide);

    timelineRightSide.addEventListener('mousedown', (e) => {
        isResizingByRightSide = true;
        isResizing = true;
        timeline.classList.add('grabbing');

        currentGrabTimelineId = timelineRightSide.dataset.timeline_id;
    });

    timelineLeftSide.addEventListener('mousedown', (e) => {
        isResizingByLeftSide = true;
        isResizing = true;
        timeline.classList.add('grabbing');

        currentGrabTimelineId = timelineLeftSide.dataset.timeline_id;
    });
}

document.addEventListener('mousemove', (e) => {
    return;
    if (isResizing && isResizingByRightSide) {
        grabByRightSide(e, currentGrabTimelineId);
    }

    if (isResizing && isResizingByLeftSide) {
        grabByLeftSide(e, currentGrabTimelineId);
    }
});

document.addEventListener('mouseup', () => {
    if (isResizing) {
        timelines.forEach((timeline) => {
            timeline.classList.remove('grabbing');
        });
        isResizing = false;
        isResizingByRightSide = false;
        isResizingByLeftSide = false;

        currentGrabTimelineId = null;
    }
});

function grabByLeftSide(e, timelineId) {
    if (!timelineId) return;
    const timeline = document.getElementById(timelineId);
    const timelineRect = timeline.getBoundingClientRect();
    const mouseX = e.clientX;

    const cells = timeline.parentElement.parentElement.querySelectorAll('td');
    let targetCellId = null;

    for (const cell of cells) {
        const cellRect = cell.getBoundingClientRect();

        if (mouseX >= cellRect.left && mouseX <= cellRect.right) {
            targetCellId = cell.id;
            break;
        }
    }

    if (!targetCellId) return;

    const targetCell = document.getElementById(targetCellId);
    const targetCellRect = targetCell.getBoundingClientRect();

    const currentWidth = parseFloat(getComputedStyle(timeline).width);
    const currentLeft = parseFloat(getComputedStyle(timeline).left);

    const newWidth = currentWidth + (currentLeft - targetCellRect.left);

    const minWidth = 15;

    if (newWidth >= minWidth) {
        timeline.style.width = newWidth + 'px';
        timeline.style.left = targetCellRect.left + 'px';
    }
}

function grabByRightSide(e, timelineId) {
    if (!timelineId) return;
    const timeline = document.getElementById(timelineId);
    if (!timeline || !timelineId) return;
    const timelineRect = timeline.getBoundingClientRect();
    const mouseX = e.clientX;

    const cells = table.querySelectorAll('td');
    let targetCellId = null;

    for (const cell of cells) {
        const cellRect = cell.getBoundingClientRect();

        if (mouseX >= cellRect.left && mouseX <= cellRect.right) {
            targetCellId = cell.id;
            break;
        }
    }

    if (targetCellId) {
        const targetCell = document.getElementById(targetCellId);
        const targetCellRect = targetCell.getBoundingClientRect();

        timeline.style.width = (targetCellRect.left + targetCellRect.width - timelineRect.left) + 'px';
    }
}

function v2grabByRightSide(timeLine, targetCell) {
    const timelineRect = timeLine.getBoundingClientRect();
    const targetCellRect = targetCell.getBoundingClientRect();

    timeLine.style.width = (targetCellRect.left + targetCellRect.width - timelineRect.left) + 'px';
}

function startAnimation(timeLine, currentWidth, finalWidth) {
    return;
    timeLine.style.animation = 'none';
    console.log(currentWidth, finalWidth)

    const animation = document.createElement('style');
    animation.innerHTML = `
      @keyframes expandWidth {
        from {
          width: ${currentWidth};
        }
        to {
          width: ${finalWidth};
        }
      }
    `;

    document.head.appendChild(animation);

    timeLine.style.animation = 'expandWidth 2s slidein';
}