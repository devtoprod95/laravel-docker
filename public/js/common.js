document.addEventListener("DOMContentLoaded", function () {
    const datepickers = document.querySelectorAll('.datepicker');

    if (typeof flatpickr !== 'undefined') {
        datepickers.forEach(function (element) {
            flatpickr(element, {
                dateFormat: "Y-m-d", // 날짜 형식
                locale: "ko",        // 한글 설정
                altInput: true,      // 입력창과 별도의 보기 전용 입력창 사용 (더 깔끔함)
                altFormat: "Y년 m월 d일", // 사용자에게 보여질 형식
                allowInput: false,    // 키보드 직접 입력 허용
                // 시간 선택 기능을 원할 경우 주석 해제
                enableTime: true,
                // time_24hr: true,
                // 달력이 화면 아래로 짤리지 않게 자동 조정
                position: "auto",
                // Tabler 스타일과 어울리는 애니메이션 효과
                animate: true,
                // 달력 헤더에 오늘 날짜로 즉시 이동하는 버튼 등 추가
                showMonths: 1,
            });
        });
    } else {
        console.error("Flatpickr가 로드되지 않았습니다.");
    }
});
