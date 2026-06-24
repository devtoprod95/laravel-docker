#!/bin/bash
echo ""
echo "=================================="
echo "  🚀 Laravel Service list - $(date '+%Y-%m-%d %H:%M:%S')"
echo "=================================="
supervisorctl status
echo "=================================="
echo "  💡 service status <name>   서비스 상태 확인"
echo "  💡 service start <name>    서비스 시작"
echo "  💡 service stop <name>     서비스 중지"
echo "  💡 service restart <name>  서비스 재시작"
echo "=================================="
echo ""
