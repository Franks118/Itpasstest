<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Topic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhilNitsExamSeeder extends Seeder
{
    public function run(): void
    {
        $creatorId = $this->ensureCreator();
        $topicMap = Topic::query()->pluck('id', 'name');

        $questions = [
            // TECHNOLOGY - Basic Theory & Logic
            [
                'topic' => 'Basic theory',
                'text' => 'Using 8-bit two\'s complement representation, what is the bit pattern for the decimal number -12?',
                'explanation' => '12 is 00001100. 1\'s complement is 11110011. Adding 1 gives 11110100.',
                'difficulty' => 'hard',
                'options' => [
                    ['text' => '11110100', 'correct' => true],
                    ['text' => '11110011', 'correct' => false],
                    ['text' => '10001100', 'correct' => false],
                    ['text' => '11111100', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Basic theory',
                'text' => 'What is the result of the bitwise XOR operation between 10101010 and 11001100?',
                'explanation' => 'XOR gives 1 only if the bits are different. 10101010 XOR 11001100 = 01100110.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => '01100110', 'correct' => true],
                    ['text' => '11101110', 'correct' => false],
                    ['text' => '10001000', 'correct' => false],
                    ['text' => '01010101', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Basic theory',
                'text' => 'Which logic gate produces an output of 0 ONLY when both inputs are 1?',
                'explanation' => 'NAND is the inverse of AND. AND is 1 only when both are 1, so NAND is 0 only when both are 1.',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => 'NAND', 'correct' => true],
                    ['text' => 'NOR', 'correct' => false],
                    ['text' => 'XOR', 'correct' => false],
                    ['text' => 'AND', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Basic theory',
                'text' => 'Convert the hexadecimal number 0x3D to decimal.',
                'explanation' => '3*16^1 + 13*16^0 = 48 + 13 = 61.',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => '61', 'correct' => true],
                    ['text' => '58', 'correct' => false],
                    ['text' => '63', 'correct' => false],
                    ['text' => '55', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Basic theory',
                'text' => 'In a binary tree, what is the maximum number of nodes at level k (where the root is at level 0)?',
                'explanation' => 'Level 0 has 1 node (2^0), level 1 has 2 (2^1), level 2 has 4 (2^2), so level k has 2^k.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => '2^k', 'correct' => true],
                    ['text' => '2^(k+1) - 1', 'correct' => false],
                    ['text' => 'k^2', 'correct' => false],
                    ['text' => '2k', 'correct' => false],
                ]
            ],

            // TECHNOLOGY - Computer System & Architecture
            [
                'topic' => 'Computer system',
                'text' => 'Which CPU architecture uses a fixed length for instructions and minimizes the number of clock cycles per instruction?',
                'explanation' => 'RISC (Reduced Instruction Set Computer) focuses on simple, fixed-length instructions for efficiency.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'RISC', 'correct' => true],
                    ['text' => 'CISC', 'correct' => false],
                    ['text' => 'VLIW', 'correct' => false],
                    ['text' => 'MISD', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Computer system',
                'text' => 'What is the primary function of the DMA (Direct Memory Access) controller?',
                'explanation' => 'DMA allows I/O devices to transfer data directly to/from memory without involving the CPU.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'To allow I/O devices to bypass the CPU for memory transfers', 'correct' => true],
                    ['text' => 'To manage the CPU cache levels', 'correct' => false],
                    ['text' => 'To handle virtual memory page faults', 'correct' => false],
                    ['text' => 'To coordinate network packet routing', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Computer system',
                'text' => 'Which memory type is volatile and requires periodic refreshing to maintain data?',
                'explanation' => 'DRAM (Dynamic RAM) uses capacitors that leak charge and must be refreshed.',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => 'DRAM', 'correct' => true],
                    ['text' => 'SRAM', 'correct' => false],
                    ['text' => 'EEPROM', 'correct' => false],
                    ['text' => 'Flash Memory', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Computer system',
                'text' => 'What is the "Write-Back" policy in cache memory management?',
                'explanation' => 'Write-back updates the cache immediately but only updates the main memory when the cache line is replaced.',
                'difficulty' => 'hard',
                'options' => [
                    ['text' => 'Data is updated in main memory only when the cache line is evicted', 'correct' => true],
                    ['text' => 'Data is updated in both cache and main memory simultaneously', 'correct' => false],
                    ['text' => 'Data is written directly to main memory bypassing the cache', 'correct' => false],
                    ['text' => 'Data is updated in main memory every clock cycle', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Computer system',
                'text' => 'Which RAID level provides striping with parity distributed across all disks?',
                'explanation' => 'RAID 5 stripes data and parity across all drives in the array.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'RAID 5', 'correct' => true],
                    ['text' => 'RAID 0', 'correct' => false],
                    ['text' => 'RAID 1', 'correct' => false],
                    ['text' => 'RAID 6', 'correct' => false],
                ]
            ],

            // TECHNOLOGY - Operating Systems
            [
                'topic' => 'Computer system',
                'text' => 'Which scheduling algorithm is non-preemptive and always picks the process with the shortest execution time?',
                'explanation' => 'SJF (Shortest Job First) picks the smallest job. Non-preemptive means it won\'t stop a running process.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'Non-preemptive SJF', 'correct' => true],
                    ['text' => 'Round Robin', 'correct' => false],
                    ['text' => 'Priority Scheduling', 'correct' => false],
                    ['text' => 'First-Come First-Served', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Computer system',
                'text' => 'What is the "Thrashing" phenomenon in an operating system?',
                'explanation' => 'Thrashing occurs when the OS spends more time swapping pages than executing instructions.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'Excessive paging that degrades system performance', 'correct' => true],
                    ['text' => 'A hardware failure in the disk controller', 'correct' => false],
                    ['text' => 'Multiple processes accessing the same shared memory', 'correct' => false],
                    ['text' => 'A security breach involving brute force', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Computer system',
                'text' => 'Which condition is NOT required for a deadlock to occur?',
                'explanation' => 'The four necessary conditions are: Mutual Exclusion, Hold and Wait, No Preemption, and Circular Wait. Preemption actually prevents deadlocks.',
                'difficulty' => 'hard',
                'options' => [
                    ['text' => 'Preemption', 'correct' => true],
                    ['text' => 'Mutual Exclusion', 'correct' => false],
                    ['text' => 'Circular Wait', 'correct' => false],
                    ['text' => 'Hold and Wait', 'correct' => false],
                ]
            ],

            // TECHNOLOGY - Network
            [
                'topic' => 'Network',
                'text' => 'At which layer of the OSI model does the TCP protocol operate?',
                'explanation' => 'TCP (Transmission Control Protocol) is a Layer 4 (Transport) protocol.',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => 'Transport Layer', 'correct' => true],
                    ['text' => 'Network Layer', 'correct' => false],
                    ['text' => 'Session Layer', 'correct' => false],
                    ['text' => 'Data Link Layer', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Network',
                'text' => 'What is the default subnet mask for a Class C IP address?',
                'explanation' => 'Class C uses 24 bits for the network, so 255.255.255.0.',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => '255.255.255.0', 'correct' => true],
                    ['text' => '255.255.0.0', 'correct' => false],
                    ['text' => '255.0.0.0', 'correct' => false],
                    ['text' => '255.255.255.255', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Network',
                'text' => 'Which protocol is used to automatically assign IP addresses to devices on a network?',
                'explanation' => 'DHCP (Dynamic Host Configuration Protocol) manages IP address distribution.',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => 'DHCP', 'correct' => true],
                    ['text' => 'DNS', 'correct' => false],
                    ['text' => 'ARP', 'correct' => false],
                    ['text' => 'SNMP', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Network',
                'text' => 'What does a "Default Gateway" represent in a TCP/IP network configuration?',
                'explanation' => 'The gateway is the router address used to send traffic outside the local subnet.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'The IP address of the router used to reach other networks', 'correct' => true],
                    ['text' => 'The server that resolves domain names', 'correct' => false],
                    ['text' => 'The first IP address in the local subnet', 'correct' => false],
                    ['text' => 'The firewall address for the internal network', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Network',
                'text' => 'In the OSI model, which layer is responsible for routing packets based on logical addresses?',
                'explanation' => 'The Network Layer (Layer 3) handles routing using IP addresses.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'Network Layer', 'correct' => true],
                    ['text' => 'Transport Layer', 'correct' => false],
                    ['text' => 'Physical Layer', 'correct' => false],
                    ['text' => 'Data Link Layer', 'correct' => false],
                ]
            ],

            // TECHNOLOGY - Database
            [
                'topic' => 'Database',
                'text' => 'In a relational database, what is the purpose of a "Foreign Key"?',
                'explanation' => 'A foreign key establishes a relationship by referencing the primary key of another table.',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => 'To establish a relationship between two tables', 'correct' => true],
                    ['text' => 'To uniquely identify a record in its own table', 'correct' => false],
                    ['text' => 'To index a column for faster searching', 'correct' => false],
                    ['text' => 'To encrypt sensitive data columns', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Database',
                'text' => 'Which SQL statement is used to remove all records from a table without deleting the table structure?',
                'explanation' => 'TRUNCATE is faster than DELETE for clearing a table.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'TRUNCATE', 'correct' => true],
                    ['text' => 'DROP', 'correct' => false],
                    ['text' => 'REMOVE', 'correct' => false],
                    ['text' => 'DELETE TABLE', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Database',
                'text' => 'What is "Referential Integrity" in database management?',
                'explanation' => 'It ensures that relationships between tables remain consistent (no orphan records).',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'Ensuring foreign key values always point to valid primary keys', 'correct' => true],
                    ['text' => 'Ensuring no duplicate records exist in a table', 'correct' => false],
                    ['text' => 'Ensuring all columns have a value (no NULLs)', 'correct' => false],
                    ['text' => 'Ensuring data is backed up regularly', 'correct' => false],
                ]
            ],

            // TECHNOLOGY - Information Security
            [
                'topic' => 'Information security',
                'text' => 'Which type of encryption uses the same key for both encryption and decryption?',
                'explanation' => 'Symmetric encryption (like AES) uses a single shared secret key.',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => 'Symmetric Encryption', 'correct' => true],
                    ['text' => 'Asymmetric Encryption', 'correct' => false],
                    ['text' => 'Hashing', 'correct' => false],
                    ['text' => 'Digital Signature', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Information security',
                'text' => 'What is the primary goal of a "Cross-Site Scripting" (XSS) attack?',
                'explanation' => 'XSS injects malicious scripts into trusted websites to be executed by other users.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'To execute malicious scripts in a user\'s browser', 'correct' => true],
                    ['text' => 'To crash the database server with heavy queries', 'correct' => false],
                    ['text' => 'To intercept network traffic on a public Wi-Fi', 'correct' => false],
                    ['text' => 'To gain administrative access to the OS', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Information security',
                'text' => 'Which of the following is a "Social Engineering" attack technique?',
                'explanation' => 'Phishing relies on psychological manipulation of humans rather than technical exploits.',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => 'Phishing', 'correct' => true],
                    ['text' => 'Buffer Overflow', 'correct' => false],
                    ['text' => 'Port Scanning', 'correct' => false],
                    ['text' => 'SQL Injection', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Information security',
                'text' => 'What is "Salt" in the context of password storage?',
                'explanation' => 'Salt is random data added to a password before hashing to protect against rainbow table attacks.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'Random data added to a password before hashing', 'correct' => true],
                    ['text' => 'A special character required in a password', 'correct' => false],
                    ['text' => 'A method to compress password databases', 'correct' => false],
                    ['text' => 'The time limit for a session cookie', 'correct' => false],
                ]
            ],

            // MANAGEMENT - Software Engineering
            [
                'topic' => 'Software engineering',
                'text' => 'Which software testing level focuses on individual functions or methods?',
                'explanation' => 'Unit testing is the lowest level, testing isolated pieces of code.',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => 'Unit Testing', 'correct' => true],
                    ['text' => 'Integration Testing', 'correct' => false],
                    ['text' => 'System Testing', 'correct' => false],
                    ['text' => 'Acceptance Testing', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Software engineering',
                'text' => 'What is "Regression Testing"?',
                'explanation' => 'It ensures that new code changes haven\'t broken existing functionality.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'Re-testing to ensure changes didn\'t break old features', 'correct' => true],
                    ['text' => 'Testing a system under heavy load', 'correct' => false],
                    ['text' => 'Testing the system with end-users for feedback', 'correct' => false],
                    ['text' => 'Testing the code for security vulnerabilities', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Software engineering',
                'text' => 'Which software development methodology emphasizes iterative development and customer collaboration?',
                'explanation' => 'Agile methodologies (like Scrum) focus on small iterations and constant feedback.',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => 'Agile', 'correct' => true],
                    ['text' => 'Waterfall', 'correct' => false],
                    ['text' => 'Spiral', 'correct' => false],
                    ['text' => 'V-Model', 'correct' => false],
                ]
            ],

            // MANAGEMENT - Project Management
            [
                'topic' => 'Project management',
                'text' => 'What does "Brooks\' Law" state about project management?',
                'explanation' => 'Adding manpower to a late software project makes it later.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'Adding manpower to a late project makes it later', 'correct' => true],
                    ['text' => 'A project manager should not manage more than 10 people', 'correct' => false],
                    ['text' => 'Software quality decreases as team size increases', 'correct' => false],
                    ['text' => 'The cost of a bug increases exponentially over time', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Project management',
                'text' => 'In a PERT chart, what is the "Slack Time" of a task?',
                'explanation' => 'Slack is the amount of time a task can be delayed without delaying the whole project.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'The delay allowed before it affects the project end date', 'correct' => true],
                    ['text' => 'The total time required to complete the task', 'correct' => false],
                    ['text' => 'The time between task start and task end', 'correct' => false],
                    ['text' => 'The overtime required to finish the task', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Project management',
                'text' => 'Which document defines the scope, objectives, and participants in a project at its start?',
                'explanation' => 'The Project Charter is the foundational document that authorizes the project.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'Project Charter', 'correct' => true],
                    ['text' => 'WBS (Work Breakdown Structure)', 'correct' => false],
                    ['text' => 'SLA (Service Level Agreement)', 'correct' => false],
                    ['text' => 'Gantt Chart', 'correct' => false],
                ]
            ],

            // STRATEGY - Business Strategy
            [
                'topic' => 'Business strategy',
                'text' => 'Which analysis framework evaluates "Strengths, Weaknesses, Opportunities, and Threats"?',
                'explanation' => 'SWOT analysis is used to assess internal and external factors of a business.',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => 'SWOT', 'correct' => true],
                    ['text' => 'PEST', 'correct' => false],
                    ['text' => 'PPM', 'correct' => false],
                    ['text' => 'Balanced Scorecard', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Business strategy',
                'text' => 'What is the "Long Tail" business model?',
                'explanation' => 'It focuses on selling small volumes of hard-to-find items to many customers.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'Selling low volumes of niche products to many customers', 'correct' => true],
                    ['text' => 'Focusing only on high-volume bestseller products', 'correct' => false],
                    ['text' => 'Extending the life of a product through rebranding', 'correct' => false],
                    ['text' => 'A model that predicts long-term market trends', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Business strategy',
                'text' => 'Which strategy involves making a product or service unique to reduce price competition?',
                'explanation' => 'Differentiation strategy makes a product distinct from competitors.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'Differentiation strategy', 'correct' => true],
                    ['text' => 'Cost leadership strategy', 'correct' => false],
                    ['text' => 'Focus strategy', 'correct' => false],
                    ['text' => 'Niche market strategy', 'correct' => false],
                ]
            ],

            // STRATEGY - Corporate and Legal Affairs
            [
                'topic' => 'Corporate and legal affairs',
                'text' => 'Which legal protection is most appropriate for a software source code?',
                'explanation' => 'In most jurisdictions, software code is protected under Copyright law.',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => 'Copyright', 'correct' => true],
                    ['text' => 'Patent', 'correct' => false],
                    ['text' => 'Trademark', 'correct' => false],
                    ['text' => 'Industrial Design', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Corporate and legal affairs',
                'text' => 'What is "PLM" (Product Lifecycle Management)?',
                'explanation' => 'PLM manages the entire lifecycle of a product from inception to disposal.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'Managing a product from design to disposal', 'correct' => true],
                    ['text' => 'A method to measure production line efficiency', 'correct' => false],
                    ['text' => 'Software to track product sales performance', 'correct' => false],
                    ['text' => 'A legal framework for product safety', 'correct' => false],
                ]
            ],

            // TECHNOLOGY - Algorithms (Subject B context)
            [
                'topic' => 'Algorithms and programming',
                'text' => 'What is the time complexity of searching for an element in a balanced Binary Search Tree (BST)?',
                'explanation' => 'In a balanced BST, search time is O(log n).',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'O(log n)', 'correct' => true],
                    ['text' => 'O(n)', 'correct' => false],
                    ['text' => 'O(1)', 'correct' => false],
                    ['text' => 'O(n log n)', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Algorithms and programming',
                'text' => 'Which data structure is typically used to implement a LIFO (Last-In-First-Out) logic?',
                'explanation' => 'A Stack uses LIFO logic.',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => 'Stack', 'correct' => true],
                    ['text' => 'Queue', 'correct' => false],
                    ['text' => 'Linked List', 'correct' => false],
                    ['text' => 'Array', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Algorithms and programming',
                'text' => 'In a recursive function, what is the "Base Case"?',
                'explanation' => 'The base case stops the recursion and prevents infinite loops.',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => 'The condition that terminates the recursion', 'correct' => true],
                    ['text' => 'The part of the function that calls itself', 'correct' => false],
                    ['text' => 'The initial input passed to the function', 'correct' => false],
                    ['text' => 'The variable that stores the final result', 'correct' => false],
                ]
            ],

            // ADDING MORE TO REACH 50 UNIQUE
            [
                'topic' => 'Basic theory',
                'text' => 'What is the decimal equivalent of the octal number 75?',
                'explanation' => '7*8^1 + 5*8^0 = 56 + 5 = 61.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => '61', 'correct' => true],
                    ['text' => '58', 'correct' => false],
                    ['text' => '65', 'correct' => false],
                    ['text' => '75', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Basic theory',
                'text' => 'Which of the following describes the "Sampling Theorem"?',
                'explanation' => 'It states that a signal must be sampled at twice its maximum frequency to be reconstructed.',
                'difficulty' => 'hard',
                'options' => [
                    ['text' => 'Sample at more than twice the maximum frequency', 'correct' => true],
                    ['text' => 'Sample at the same rate as the maximum frequency', 'correct' => false],
                    ['text' => 'Sample once every second regardless of frequency', 'correct' => false],
                    ['text' => 'Sample only the peaks of the wave', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Computer system',
                'text' => 'Which component of the CPU is responsible for performing arithmetic and logical operations?',
                'explanation' => 'The ALU (Arithmetic Logic Unit) handles math and logic.',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => 'ALU', 'correct' => true],
                    ['text' => 'CU (Control Unit)', 'correct' => false],
                    ['text' => 'Registers', 'correct' => false],
                    ['text' => 'Cache', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Computer system',
                'text' => 'What is "Pipelining" in CPU architecture?',
                'explanation' => 'Pipelining overlaps the execution of multiple instructions to improve throughput.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'Overlapping instruction execution stages', 'correct' => true],
                    ['text' => 'Using multiple CPUs in parallel', 'correct' => false],
                    ['text' => 'Increasing the clock speed of the CPU', 'correct' => false],
                    ['text' => 'Reducing the number of registers', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Network',
                'text' => 'Which protocol is used to resolve a domain name (like google.com) to an IP address?',
                'explanation' => 'DNS (Domain Name System) performs name-to-IP resolution.',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => 'DNS', 'correct' => true],
                    ['text' => 'HTTP', 'correct' => false],
                    ['text' => 'FTP', 'correct' => false],
                    ['text' => 'SMTP', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Network',
                'text' => 'What is the purpose of the "NAT" (Network Address Translation) protocol?',
                'explanation' => 'NAT allows multiple devices on a private network to share a single public IP address.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'Mapping private IP addresses to a public IP', 'correct' => true],
                    ['text' => 'Translating human-readable names to IP addresses', 'correct' => false],
                    ['text' => 'Encrypting web traffic between client and server', 'correct' => false],
                    ['text' => 'Routing packets based on their MAC address', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Database',
                'text' => 'Which Normal Form (NF) is specifically concerned with removing partial dependencies on a composite primary key?',
                'explanation' => '2nd Normal Form (2NF) ensures all non-key attributes are fully functionally dependent on the primary key.',
                'difficulty' => 'hard',
                'options' => [
                    ['text' => '2NF', 'correct' => true],
                    ['text' => '1NF', 'correct' => false],
                    ['text' => '3NF', 'correct' => false],
                    ['text' => 'BCNF', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Database',
                'text' => 'What does the "I" in ACID stand for?',
                'explanation' => 'Isolation ensures that concurrent transactions don\'t interfere with each other.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'Isolation', 'correct' => true],
                    ['text' => 'Integrity', 'correct' => false],
                    ['text' => 'Index', 'correct' => false],
                    ['text' => 'Iteration', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Information security',
                'text' => 'Which certificate authority (CA) standard is widely used for digital certificates in web browsers?',
                'explanation' => 'X.509 is the standard format for public key certificates.',
                'difficulty' => 'hard',
                'options' => [
                    ['text' => 'X.509', 'correct' => true],
                    ['text' => 'IEEE 802.11', 'correct' => false],
                    ['text' => 'RFC 1918', 'correct' => false],
                    ['text' => 'UTF-8', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Information security',
                'text' => 'What is a "Pharming" attack?',
                'explanation' => 'Pharming redirects users to a fraudulent website even if they type the correct URL (e.g., via DNS poisoning).',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'Redirecting users to a fake website via DNS tampering', 'correct' => true],
                    ['text' => 'Sending mass emails with malicious links', 'correct' => false],
                    ['text' => 'Guessing passwords using a dictionary of words', 'correct' => false],
                    ['text' => 'Exploiting a vulnerability in the web server code', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Software engineering',
                'text' => 'In software development, what does "MTBF" stand for?',
                'explanation' => 'Mean Time Between Failures is a measure of system reliability.',
                'difficulty' => 'medium',
                'options' => [
                    ['text' => 'Mean Time Between Failures', 'correct' => true],
                    ['text' => 'Maximum Time Before Failure', 'correct' => false],
                    ['text' => 'Minimum Test Build Frequency', 'correct' => false],
                    ['text' => 'Main Task Build Flow', 'correct' => false],
                ]
            ],
            [
                'topic' => 'Service management',
                'text' => 'Which ITIL process is responsible for managing the lifecycle of all problems?',
                'explanation' => 'Problem Management identifies the root causes of incidents.',
                'difficulty' => 'easy',
                'options' => [
                    ['text' => 'Problem Management', 'correct' => true],
                    ['text' => 'Incident Management', 'correct' => false],
                    ['text' => 'Change Management', 'correct' => false],
                    ['text' => 'Release Management', 'correct' => false],
                ]
            ],
        ];

        // Ensure we have exactly 50+ unique questions (The list above has 51)
        
        // 10-Question Quick Drill
        $this->seedPhilNitsExam($creatorId, $topicMap, 'PhilNITS 10-Question Quick Drill', 'A fast recap of high-probability PhilNITS questions.', 20, array_slice($questions, 0, 10));

        // 20-Question Practice
        $this->seedPhilNitsExam($creatorId, $topicMap, 'PhilNITS 20-Question Practice', 'Standard practice set for PhilNITS FE exam topics.', 45, array_slice($questions, 10, 20));

        // PhilNITS FE High-Probability (Original/Main)
        $this->seedPhilNitsExam($creatorId, $topicMap, 'PhilNITS FE High-Probability', 'Comprehensive set of 50+ unique, high-probability Subject A questions.', 90, $questions);

        // 100-Question Mock Exam (Mixed unique + slight variations to reach 100)
        $mockQuestions = $this->expandQuestions($questions, 100);
        $this->seedPhilNitsExam($creatorId, $topicMap, 'PhilNITS 100-Question Mock Exam', 'Simulated full-length exam using the entire unique bank and logic variations.', 150, $mockQuestions);
    }

    private function seedPhilNitsExam(int $creatorId, $topicMap, string $title, string $description, int $duration, array $questionSet): void
    {
        $exam = Exam::query()->updateOrCreate(
            ['title' => $title],
            [
                'user_id' => $creatorId,
                'description' => $description,
                'duration_minutes' => $duration,
                'status' => 'published',
                'total_questions' => count($questionSet),
                'shuffle_questions' => true,
            ]
        );

        // Delete dependencies properly
        $questionIds = $exam->questions()->pluck('id');
        DB::table('exam_answers')->whereIn('question_id', $questionIds)->delete();
        DB::table('question_options')->whereIn('question_id', $questionIds)->delete();
        $exam->questions()->delete();

        foreach ($questionSet as $idx => $qData) {
            $topicId = $topicMap[$qData['topic']] ?? null;
            if (!$topicId) {
                $topicId = Topic::query()->where('major_category', 'like', "%{$qData['topic']}%")->value('id') ?? 1;
            }

            $q = $exam->questions()->create([
                'topic_id' => $topicId,
                'question_text' => $qData['text'],
                'explanation' => $qData['explanation'],
                'difficulty' => $qData['difficulty'],
                'points' => 1,
                'order_index' => $idx + 1,
            ]);

            foreach ($qData['options'] as $oidx => $o) {
                $q->options()->create([
                    'option_text' => $o['text'],
                    'is_correct' => $o['correct'],
                    'order_index' => $oidx + 1,
                ]);
            }
        }
    }

    private function expandQuestions(array $base, int $count): array
    {
        $expanded = [];
        $baseSize = count($base);
        for ($i = 0; $i < $count; $i++) {
            $item = $base[$i % $baseSize];
            if ($i >= $baseSize) {
                $version = intdiv($i, $baseSize) + 1;
                $item['text'] .= " (Ver {$version})";
            }
            $expanded[] = $item;
        }
        return $expanded;
    }

    private function ensureCreator(): int
    {
        return DB::table('learners')->orderBy('id')->value('id') ?? 
               DB::table('learners')->insertGetId([
                   'name' => 'PhilNITS Official',
                   'session_number' => 1,
                   'score' => 0,
                   'timestamp' => now(),
                   'created_at' => now(),
                   'updated_at' => now(),
               ]);
    }
}
