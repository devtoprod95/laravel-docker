window.salert = async function(options = {}) {
    const result = await Swal.fire({
        title: options.title || '',
        text: options.text || '',
        html: options.html || null, // HTML 속성 추가
        icon: options.icon || 'warning',
        showCancelButton: options.cancel !== false,
        reverseButtons: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '확인',
        cancelButtonText: '취소',
        timer: options.timer || 0, // 후에 자동으로 닫힘
        timerProgressBar: options.timer || false // 진행률 바를 보여주어 남은 시간을 시각화
    });

    return result.isConfirmed;
};

document.addEventListener("DOMContentLoaded", function () {
    const startInput = document.querySelector('input[name="startDate"]');
    const endInput = document.querySelector('input[name="endDate"]');

    let startPicker;
    let endPicker;

    if (startInput && endInput && typeof flatpickr !== 'undefined') {
        const config = {
            dateFormat: "Y-m-d",
            locale: "ko",
            altInput: true,
            altFormat: "Y년 m월 d일"
        };

        // 1. 시작일 초기화
        startPicker = flatpickr(startInput, {
            ...config,
            onChange: (selectedDates, dateStr) => {
                if (endPicker) endPicker.set('minDate', dateStr);
            },
            onReady: function(sd, ds, inst) {
                addControls(inst, () => startPicker, () => endPicker);
                // 초기 값 존재 시 종료일 minDate 설정
                if (ds) endPicker?.set('minDate', ds);
            }
        });

        // 2. 종료일 초기화
        endPicker = flatpickr(endInput, {
            ...config,
            onChange: (selectedDates, dateStr) => {
                if (startPicker) startPicker.set('maxDate', dateStr);
            },
            onReady: function(sd, ds, inst) {
                addControls(inst, () => startPicker, () => endPicker);
                // 초기 값 존재 시 시작일 maxDate 설정
                if (ds) startPicker?.set('maxDate', ds);
            }
        });

        setTimeout(() => {
            if (startInput.value) {
                endPicker.set('minDate', startInput.value);
            }
            if (endInput.value) {
                startPicker.set('maxDate', endInput.value);
            }
        }, 100);
    }
});

// 버튼 추가 로직 (이전과 동일)
function addControls(instance, getStart, getEnd) {
    const container = instance.calendarContainer;
    const today = new Date();

    const btnGroup = document.createElement("div");
    btnGroup.className = "d-flex gap-2 mt-2";

    const todayBtn = document.createElement("button");
    todayBtn.type = "button";
    todayBtn.className = "btn btn-sm btn-outline-primary w-100";
    todayBtn.innerText = "오늘";
    todayBtn.onclick = () => {
        if ((instance.config.minDate && today < instance.config.minDate) ||
            (instance.config.maxDate && today > instance.config.maxDate)) {
            alert("선택하신 기간 범위를 벗어납니다.");
        } else {
            instance.setDate(today);
        }
    };

    const resetBtn = document.createElement("button");
    resetBtn.type = "button";
    resetBtn.className = "btn btn-sm btn-outline-secondary w-100";
    resetBtn.innerText = "리셋";
    resetBtn.onclick = () => {
        instance.clear();
        getStart().set('maxDate', null);
        getEnd().set('minDate', null);
    };

    btnGroup.appendChild(todayBtn);
    btnGroup.appendChild(resetBtn);
    container.appendChild(btnGroup);
}

function getFormParams(formId) {
    var params = {};
    $(formId).find(":input").each(function() {
        var name = $(this).attr("name");
        if (!name) return;

        if ($(this).is(":checkbox")) {
            var key = name.replace("[]", "");
            if (!params[key]) params[key] = [];
            if ($(this).is(":checked")) params[key].push($(this).val());

        } else if ($(this).is(":radio")) {
            if ($(this).is(":checked")) params[name] = $(this).val();  // 체크된 것만

        } else {
            params[name] = $(this).val();
        }
    });

    return params;
}

// Tabulator 전역 기본값 설정
const tabulatorSettings = {
    // 페이지네이션
    sortMode: "remote",
    pagination: true,
    paginationMode: "remote",
    paginationSizeSelector: [30, 50, 100, 200],
    paginationSize: 30,
    paginationButtonCount: 10,
    paginationCounter: function(pageSize, currentRow, currentPage, totalRows, totalPages) {
        var total = window._tableTotalCount || totalRows;
        return `전체 <strong>${total}</strong>건 / <strong>${currentPage}</strong> 페이지`;
    },

    // 레이아웃
    layout: "fitColumns",
    renderVertical: "virtual",
    placeholder: "데이터가 없습니다.",
    height: "600px",

    // 로케일
    locale: "ko",
    initialLocale: "ko",
    langs: {
        "ko": {
            "pagination": {
                "first": "처음", "last": "마지막", "prev": "이전", "next": "다음",
                "first_title": "첫 페이지", "last_title": "마지막 페이지", "prev_title": "이전 페이지", "next_title": "다음 페이지",
                "page_size": "페이지당 표시",
            }
        }
    },

    // 선택 / 범위
    selectableRows: undefined,
    fillHandle: "table",
    selectableRange:1,
    selectableRangeColumns:true,
    selectableRangeRows:true,
    headerSortClickElement: "icon",

    // 클립보드
    clipboard: true,
    clipboardCopyStyled: false,
    clipboardCopyRowRange: "range",
    clipboardCopyConfig: {
        columnHeaders: false,
    },

    // 컬럼 기본값
    columnDefaults: {
        hozAlign: "center",
        headerHozAlign: "center",
        headerSort: false,
    },

    ajaxParams: function() {
        return getFormParams("#searchForm");
    },
    ajaxResponse: function(url, params, response) {
        const urlObj = new URL(window.location);

        // 중첩된 객체/배열을 평탄화하여 URL 파라미터로 변환하는 함수
        const flattenParams = (obj, prefix = '') => {
            Object.keys(obj).forEach(key => {
                const value = obj[key];
                const newKey = prefix ? `${prefix}[${key}]` : key;

                if (value !== null && typeof value === 'object') {
                    flattenParams(value, newKey); // 재귀 호출
                } else if (value !== undefined && value !== "" && value !== null) {
                    urlObj.searchParams.set(newKey, value);
                }
            });
        };

        // 기존의 모든 파라미터 초기화 후 재설정 (선택 사항)
        urlObj.search = "";

        // 파라미터 평탄화 실행
        flattenParams(params);

        window.history.replaceState({}, '', urlObj);
        window._tableTotalCount = response.total;
        return response;
    },
};
Object.assign(Tabulator.defaultOptions, tabulatorSettings);

window.tabulatorHeaderMenu = function() {
    var menu = [];
    // 1. 전체 컬럼 중 headerMenu 설정이 있는 컬럼만 필터링
    var columns = this.getColumns().filter(col => col.getDefinition().headerMenu !== undefined);

    for (let column of columns) {
        // Tabler 아이콘 (ti-checkbox, ti-square)
        const iconCheck = '<i class="ti ti-checkbox"></i>';
        const iconUncheck = '<i class="ti ti-square"></i>';

        let label = document.createElement("span");
        label.classList.add("d-flex", "align-items-center", "gap-2");

        label.innerHTML = (column.isVisible() ? iconCheck : iconUncheck) +
                        `<span> ${column.getDefinition().title}</span>`;

        menu.push({
            label: label,
            action: function(e) {
                e.stopPropagation();
                column.toggle();

                let icon = label.querySelector("i");
                if (column.isVisible()) {
                    icon.className = "ti ti-checkbox";
                } else {
                    icon.className = "ti ti-square";
                }
            }
        });
    }

    return menu;
};
