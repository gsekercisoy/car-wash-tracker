package com.otoyikama.api.controller;

import com.otoyikama.api.entity.SmsLog;
import com.otoyikama.api.repository.SmsLogRepository;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@RequestMapping("/api/sms-log")
public class SmsLogController {

    private final SmsLogRepository repo;

    public SmsLogController(SmsLogRepository repo) {
        this.repo = repo;
    }

    @GetMapping
    public List<SmsLog> liste() {
        return repo.findTop200ByOrderByIdDesc();
    }
}
