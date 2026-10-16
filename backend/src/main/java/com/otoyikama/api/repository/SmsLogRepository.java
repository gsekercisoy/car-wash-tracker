package com.otoyikama.api.repository;

import com.otoyikama.api.entity.SmsLog;
import org.springframework.data.jpa.repository.JpaRepository;
import java.util.List;

public interface SmsLogRepository extends JpaRepository<SmsLog, Long> {
    List<SmsLog> findTop200ByOrderByIdDesc();
}
